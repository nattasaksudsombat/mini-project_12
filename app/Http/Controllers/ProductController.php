<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Validation\Rule;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Models\ProductColorSize;
use Picqer\Barcode\BarcodeGeneratorPNG; // ต้องใช้ library barcode
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;





class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with(['category', 'productImages', 'productOptions', 'productColors', 'productTags']);

        $search = $request->input('search');

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('id_stock', 'like', "%{$search}%");
                })
                    ->where('is_active', 1); // ✅ แสดงเฉพาะสินค้าที่เปิดใช้งาน
            })
            ->with(['category', 'productImages', 'productOptions', 'productColors', 'productTags'])
            ->get();
        $products = Product::latest()->paginate(10); // 10 รายการต่อหน้า

        return view('products.index', compact('products'));
    }
     public function search(Request $request)
    {
        $q = $request->input('q', '');
        
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('id_stock', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'id_stock', 'price')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function show(Product $product)
{
    // สถานะออเดอร์ที่ถือว่า "เปิด" (ใช้แค่เพื่อแสดงรายการ hold; ไม่เกี่ยวกับการคำนวณสต๊อค)
    $openStatuses = ['pending', 'processing'];

    // หา table variant (รองรับทั้งชื่อเอก/พหูพจน์)
    $variantTable = Schema::hasTable('product_color_size')
        ? 'product_color_size'
        : (Schema::hasTable('product_color_sizes') ? 'product_color_sizes' : null);

    if (!$variantTable) {
        abort(500, "ไม่พบตาราง product_color_size / product_color_sizes");
    }

    // อ่านสต๊อคจากวิว v_current_stock เท่านั้น (Golden Rule)
    // ต้องมีคอลัมน์ id (หรือ variant_id) + current_stock, reserved_stock, available_stock
    $stockRows = DB::table('v_current_stock as v')
        ->join("$variantTable as pcs", 'pcs.id', '=', 'v.id')  // v.id = variant_id
        ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
        ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
        ->where('v.product_id', $product->id)
        ->orderBy('c.name')
        ->orderBy('s.size_name')
        ->get([
            DB::raw('v.id as variant_id'),
            'pcs.product_id',
            'pcs.color_id',
            'pcs.size_id',
            DB::raw("COALESCE(c.name, '') as color_name"),
            DB::raw("COALESCE(s.size_name, '') as size_name"),
            'v.current_stock',
            'v.reserved_stock',
            'v.available_stock',
        ]);

    // กลุ่มตามสี -> แสดงเป็นตารางย่อยตามขนาด
    $variantsByColor = $stockRows->groupBy('color_name');

    // เตรียม map เพื่อใช้ใน Blade (เผื่อมีส่วนที่อ้างเป็น array)
    $reservedByVariantId  = $stockRows->mapWithKeys(fn($r) => [(int)$r->variant_id => (int)$r->reserved_stock])->toArray();
    $availableByVariantId = $stockRows->mapWithKeys(fn($r) => [(int)$r->variant_id => (int)$r->available_stock])->toArray();
    $onhandByVariantId    = $stockRows->mapWithKeys(fn($r) => [(int)$r->variant_id => (int)$r->current_stock])->toArray();

    // รายการออเดอร์ที่ "กำลังจับ" (จาก stock_holds.status='active') สำหรับ modal/tooltip
    $holdsRows = [];
    if (Schema::hasTable('stock_holds')) {
        // เลือก column หมายเลขออเดอร์ตามที่มีอยู่จริง
        $orderNumberExpr = Schema::hasColumn('orders', 'order_number') ? 'o.order_number'
            : (Schema::hasColumn('orders', 'code')     ? 'o.code'
            : (Schema::hasColumn('orders', 'order_no') ? 'o.order_no' : 'o.id'));

        $h = DB::table('stock_holds as sh')
            ->join("$variantTable as pcs", 'pcs.id', '=', 'sh.product_color_size_id')
            ->leftJoin('orders as o', 'o.id', '=', 'sh.order_id')
            ->where('pcs.product_id', $product->id)
            ->where('sh.status', 'active')
            ->when(Schema::hasTable('orders'), function ($q) use ($openStatuses) {
                // ถ้าตาราง orders มีอยู่ คัดเฉพาะออเดอร์เปิด (ตามที่คุณต้องการ)
                $q->whereIn('o.status', $openStatuses);
            })
            ->orderByDesc('sh.updated_at')
            ->get([
                DB::raw('sh.product_color_size_id as variant_id'),
                'sh.order_id',
                DB::raw("$orderNumberExpr as order_number"),
                'o.status',
                'sh.quantity',
            ]);

        foreach ($h as $r) {
            $holdsRows[(int)$r->variant_id][] = [
                'order_id'     => (int)($r->order_id ?? 0),
                'order_number' => (string)($r->order_number ?? ''),
                'status'       => (string)($r->status ?? ''),
                'quantity'     => (int)$r->quantity,
            ];
        }
    }

    // ส่งไปให้ view
    return view('products.show', [
        'product'             => $product,
        'variantsByColor'     => $variantsByColor,
        // สำหรับส่วนที่อาจอิง array เดิมใน Blade
        'reservedByVariantId' => $reservedByVariantId,
        'availableByVariantId'=> $availableByVariantId,
        'onhandByVariantId'   => $onhandByVariantId,
        'holdsRows'           => $holdsRows,
        'openStatuses'        => $openStatuses,
    ]);
}
public function updateVariantStock(Request $request, $productId, $variantId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $variantTable = $this->guessVariantTable(['product_color_size', 'product_color_sizes']);
        
        if (!$variantTable) {
            return back()->with('error', 'ไม่พบตารางสต็อก');
        }

        DB::table($variantTable)
            ->where('id', $variantId)
            ->where('product_id', $productId)
            ->update(['quantity' => $request->quantity]);

        return back()->with('success', 'อัปเดตสต็อกสำเร็จ');
    }
     
    public function getHoldingOrders(Request $request)
    {
        $productId = $request->input('product_id');
        $colorId   = $request->input('color_id');
        $sizeId    = $request->input('size_id');

        if (!$productId) {
            return response()->json(['error' => 'Product ID required'], 400);
        }

        // สถานะที่ถือว่า "กำลังจับสต็อก"
        $openStatuses = ['pending', 'processing'];

        // ชื่อตาราง variant
        $variantTable = $this->guessVariantTable(['product_color_size', 'product_color_sizes']);

        if (!$variantTable) {
            return response()->json(['error' => 'Variant table not found'], 500);
        }

        // Query base
        $query = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('oi.product_id', $productId)
            ->whereIn('o.status', $openStatuses);

        // ตรวจสอบคอลัมน์ที่มีอยู่
        $variantIdCol = $this->pickCol('order_items', ['product_color_size_id', 'color_size_id', 'variant_id']);
        $colorIdCol = $this->pickCol('order_items', ['color_id']);
        $sizeIdCol  = $this->pickCol('order_items', ['size_id']);

        // กรณีมี variant_id ใน order_items
        if ($variantIdCol && $colorId && $sizeId) {
            // หา variant_id จาก color_id + size_id
            $variant = DB::table($variantTable)
                ->where('product_id', $productId)
                ->where('color_id', $colorId)
                ->where('size_id', $sizeId)
                ->first(['id']);

            if ($variant) {
                $query->where("oi.$variantIdCol", $variant->id);
            } else {
                return response()->json(['orders' => [], 'total_quantity' => 0]);
            }
        }
        // กรณีเก็บแยกเป็น color_id + size_id
        elseif ($colorIdCol && $sizeIdCol && $colorId && $sizeId) {
            $query->where("oi.$colorIdCol", $colorId)
                  ->where("oi.$sizeIdCol", $sizeId);
        }
        // ไม่สามารถระบุ variant ได้
        else {
            return response()->json(['orders' => [], 'total_quantity' => 0]);
        }

        // เลือกคอลัมน์หมายเลขออเดอร์และชื่อลูกค้า
        $orderNumberCol = $this->pickExpr('orders', 'o', ['order_number', 'code', 'order_no'], 'o.id');
        $customerNameCol = $this->pickExpr('orders', 'o', ['customer_name'], "''");

        // ดึงข้อมูล
        $orders = $query
            ->select([
                'o.id as order_id',
                DB::raw("$orderNumberCol as order_number"),
                DB::raw("$customerNameCol as customer_name"),
                'o.status',
                'o.created_at',
                'oi.quantity'
            ])
            ->orderBy('o.created_at', 'desc')
            ->get();

        // คำนวณยอดรวม
        $totalQty = $orders->sum('quantity');

        return response()->json([
            'orders' => $orders,
            'total_quantity' => (int) $totalQty,
            'product_id' => (int) $productId,
            'color_id' => $colorId ? (int) $colorId : null,
            'size_id' => $sizeId ? (int) $sizeId : null
        ]);
    }

    /* ================== Helpers ================== */

    /** เดาชื่อโต๊ะจากรายการที่ให้มา */
    private function guessVariantTable(array $candidates): ?string
    {
        foreach ($candidates as $name) {
            if (Schema::hasTable($name)) return $name;
        }
        return null;
    }

    /** เลือกคอลัมน์แรกที่มีจริงในตาราง (คืนชื่อคอลัมน์เปล่า) */
    private function pickCol(string $table, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $c)) return $c;
        }
        return null;
    }

    /** สร้าง expression อ้างอิงคอลัมน์แรกที่มีจริง (เช่น 's.size_name'), ถ้าไม่มีคืน fallback */
    private function pickExpr(string $table, string $alias, array $candidates, string $fallback = "''"): string
    {
        foreach ($candidates as $c) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $c)) {
                return "$alias.$c";
            }
        }
        return $fallback;
    }
    
    public function edit(Product $product)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $product->load('tags'); // โหลด tag ที่ผูกกับสินค้านี้
        return view('products.edit', compact('product', 'categories', 'tags'));
    }
    public function create()
    {
        // ดึงข้อมูลที่จำเป็นมาใช้ในฟอร์ม เช่น หมวดสินค้า สี ขนาด แท็ก
        $categories = Category::all();
        $tags = Tag::all();
        $colors = Color::all();
        $sizes = Size::all();

        return view('products.create', compact('categories', 'tags', 'colors', 'sizes'));
    }
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'id_stock' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $product->update([
            'id_stock' => $request->id_stock,
            'name' => $request->name,
            'price' => $request->price,
            'cost' => $request->cost,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        // Sync tags
        $product->tags()->sync($request->tags ?? []);

        return redirect()->route('products.show', $product->id)->with('success', 'แก้ไขสินค้าสำเร็จ');
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_stock' => 'required|unique:products,id_stock',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $product = new Product();
        $product->id_stock = $request->id_stock;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->cost = $request->cost;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->is_active = 1;

        // upload image
        if ($request->hasFile('image')) {
            $product->save(); // save first to get product ID
            $imagePath = $request->file('image')->store('product_images', 'public');
            $product->productImages()->create([
                'image_url' => $imagePath
            ]);
        } else {
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'เพิ่มสินค้าสำเร็จ');
    }
    public function setMain(Product $product, ProductImage $image)
    {
        // ตรวจสอบว่า image นี้เป็นของสินค้านี้จริง
        if ($image->product_id !== $product->id) {
            abort(403, 'ไม่สามารถตั้งรูปนี้เป็นหลักได้');
        }

        // รีเซ็ตรูปหลักทั้งหมดของสินค้านี้
        ProductImage::where('product_id', $product->id)->update(['is_main' => false]);

        // ตั้งรูปนี้เป็นรูปหลัก
        $image->is_main = true;
        $image->save();

        return redirect()->back()->with('success', 'ตั้งรูปหลักเรียบร้อยแล้ว');
    }
    public function toggle(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return redirect()->back()->with('success', 'อัปเดตสถานะสินค้าสำเร็จ');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new ProductImport, $request->file('file'));

        return back()->with('success', 'นำเข้าข้อมูลสำเร็จ!');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
    public function getVariants($id)
{
    $variants = ProductColorSize::with(['color', 'size'])
        ->where('product_id', $id)
        ->get()
        ->map(function ($variant) {
            $colorName = $variant->color ? $variant->color->name : 'ไม่ระบุสี';
            $sizeName = $variant->size ? $variant->size->name : 'ไม่ระบุไซส์';

            return [
                'id' => $variant->id,
                'quantity' => $variant->quantity,
                'color_id' => $variant->color_id,
                'size_id' => $variant->size_id,
                'color_name' => $colorName,
                'size_name' => $sizeName,
                'display_name' => "{$colorName} - {$sizeName} (คงเหลือ: {$variant->quantity})"
            ];
        });

    return response()->json($variants);
}
public function updateTracking(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $order->tracking_number = $request->tracking_number;
    $order->save();

    return redirect()->route('orders.show', $id)->with('success', 'อัปเดต Tracking Number สำเร็จ');
}


public function printBarcode(Request $request)
{
    $variant = ProductColorSize::with(['product', 'color', 'size'])->findOrFail($request->variant_id);

    $idStock = $variant->product->id_stock; // ใช้เฉพาะรหัสสินค้า
    $codeText = $idStock . ' ' . $variant->color->name . ' ' . $variant->size->size_name;

    $generator = new BarcodeGeneratorPNG();
    
    // บาร์โค้ดใช้แค่ id_stock เท่านั้น เพื่อให้สั้นลง
    $barcode = base64_encode($generator->getBarcode($idStock, $generator::TYPE_CODE_128));

    return view('products.barcode-preview', [
        'barcode' => $barcode,         // บาร์โค้ด: id_stock
        'codeText' => $codeText,       // ชื่อใต้บาร์โค้ด: id_stock + สี + ไซส์
        'quantity' => $request->quantity,
    ]);
}

public function scanBarcode(Request $request)
{
    $code = $request->input('code'); // เช่น P123|แดง-L
    [$stockCode, $colorNameSize] = explode('|', $code);
    [$colorName, $sizeName] = explode('-', $colorNameSize);

    $product = Product::where('id_stock', $stockCode)->first();

    if (!$product) {
        return response()->json(['success' => false]);
    }

    $variant = ProductColorSize::where('product_id', $product->id)
        ->whereHas('color', fn($q) => $q->where('name', $colorName))
        ->whereHas('size', fn($q) => $q->where('size_name', $sizeName))
        ->first();

    if (!$variant) {
        return response()->json(['success' => false]);
    }

    return response()->json([
        'success' => true,
        'id_stock' => $stockCode,
        'color' => $colorName,
        'size' => $sizeName,
        'variant_id' => $variant->id
    ]);
}

}
