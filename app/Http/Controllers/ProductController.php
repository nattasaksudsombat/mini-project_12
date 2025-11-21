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

    /**
     * ==================================================================
     * ✅ ฟังก์ชันที่แก้ไขแล้ว (Search Function)
     * ==================================================================
     *
     * แก้ไข Query ให้ค้นหาจากตาราง categories และ tags ที่เกี่ยวข้องด้วย
     * เพื่อให้ตรงตามขอบเขตโครงงาน 2.1
     */
    public function search(Request $request)
    {
        $q = $request->input('q', '');

        // 1. ยังคงเงื่อนไขเดิม: ต้องพิมพ์อย่างน้อย 2 ตัวอักษร
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // 2. แก้ไข Query ให้ JOIN ตารางที่เกี่ยวข้อง
        $products = Product::query()
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('product_tags', 'products.id', '=', 'product_tags.product_id')
            ->leftJoin('tags', 'product_tags.tag_id', '=', 'tags.id')
            ->where('products.is_active', 1) // 3. ยังคงเงื่อนไขเดิม: ค้นหาเฉพาะสินค้าที่ Active
            ->where(function ($query) use ($q) {
                // 4. เพิ่มเงื่อนไขการค้นหาในตารางที่ JOIN มา
                $query->where('products.name', 'like', "%{$q}%")
                    ->orWhere('products.id_stock', 'like', "%{$q}%")
                    ->orWhere('categories.category_name', 'like', "%{$q}%") // <-- ค้นหาชื่อหมวดหมู่
                    ->orWhere('tags.tag_name', 'like', "%{$q}%");            // <-- ค้นหาชื่อแท็ก
            })
            // 5. ระบุ table 'products.' ให้ชัดเจนป้องกันคอลัมน์ซ้ำ
            ->select('products.id', 'products.name', 'products.id_stock', 'products.price')
            ->distinct() // 6. ป้องกันสินค้าซ้ำซ้อน (หากเจอทั้งชื่อและแท็ก)
            ->limit(10)
            ->get();

        return response()->json($products);
    }
    // ==================================================================
    // (จบส่วนที่แก้ไข)
    // ==================================================================

    public function show(Product $product)
{
    // ออเดอร์ที่ถือว่าเปิด
    $openStatuses = ['pending', 'processing'];

    // ชื่อตาราง variant (รองรับเอก/พหูพจน์)
    $variantTable = Schema::hasTable('product_color_size')
        ? 'product_color_size'
        : (Schema::hasTable('product_color_sizes') ? 'product_color_sizes' : null);

    if (!$variantTable) {
        abort(500, "ไม่พบตาราง product_color_size / product_color_sizes");
    }

    // ====== ตรวจชื่อคอลัมน์ใน view v_current_stock แบบยืดหยุ่น ======
    // คอลัมน์ id ของ variant
    $vIdCol = Schema::hasColumn('v_current_stock', 'variant_id') ? 'variant_id'
            : (Schema::hasColumn('v_current_stock', 'product_color_size_id') ? 'product_color_size_id'
            : 'id'); // fallback

    // คอลัมน์ stock metrics (เผื่อโปรเจ็กต์เก่าใช้ชื่ออื่น)
    $currentCol  = Schema::hasColumn('v_current_stock', 'current_stock')   ? 'current_stock'
                 : (Schema::hasColumn('v_current_stock', 'onhand')         ? 'onhand' : 'current_stock');
    $reservedCol = Schema::hasColumn('v_current_stock', 'reserved_stock')  ? 'reserved_stock'
                 : (Schema::hasColumn('v_current_stock', 'reserved')       ? 'reserved' : 'reserved_stock');
    $availCol    = Schema::hasColumn('v_current_stock', 'available_stock') ? 'available_stock'
                 : (Schema::hasColumn('v_current_stock', 'available')      ? 'available' : 'available_stock');

    // ====== ดึงสต๊อกจากวิว (Golden Rule) ======
    $stockRows = DB::table('v_current_stock as v')
        // JOIN ด้วยคอลัมน์ id ของ view ที่ตรวจพบ
        ->join("$variantTable as pcs", 'pcs.id', '=', DB::raw("v.`$vIdCol`"))
        ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
        ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
        ->where('v.product_id', $product->id)
        ->orderByRaw('COALESCE(c.name, "") ASC')
        ->orderByRaw('COALESCE(s.size_name, "") ASC')
        ->get([
            DB::raw("v.`$vIdCol` as variant_id"),                 // ✅ ไม่อ้าง v.id ตายตัวอีก
            'pcs.product_id',
            'pcs.color_id',
            'pcs.size_id',
            DB::raw("COALESCE(c.name, '')      as color_name"),
            DB::raw("COALESCE(s.size_name, '') as size_name"),
            DB::raw("v.`$currentCol`  as current_stock"),
            DB::raw("v.`$reservedCol` as reserved_stock"),
            DB::raw("v.`$availCol`    as available_stock"),
        ]);

    // group ใน PHP (ไม่แตะ GROUP BY ของ MySQL)
    $variantsByColor       = $stockRows->groupBy('color_name');
    $reservedByVariantId   = $stockRows->pluck('reserved_stock',  'variant_id')->map(fn($n)=>(int)$n)->toArray();
    $availableByVariantId  = $stockRows->pluck('available_stock', 'variant_id')->map(fn($n)=>(int)$n)->toArray();
    $onhandByVariantId     = $stockRows->pluck('current_stock',   'variant_id')->map(fn($n)=>(int)$n)->toArray();

    // ====== รายการ hold แบบยกเว้นออเดอร์ปิด (ถ้ามีตารางอยู่) ======
    $holdsRows = [];
    if (Schema::hasTable('stock_holds')) {
        // คอลัมน์เลขที่ออเดอร์ (เลือกที่มีจริง)
        $orderNumberExpr = Schema::hasColumn('orders', 'order_number') ? 'o.order_number'
            : (Schema::hasColumn('orders', 'code')     ? 'o.code'
            : (Schema::hasColumn('orders', 'order_no') ? 'o.order_no' : 'o.id'));

        $h = DB::table('stock_holds as sh')
            ->join("$variantTable as pcs", 'pcs.id', '=', 'sh.product_color_size_id')
            ->leftJoin('orders as o', 'o.id', '=', 'sh.order_id')
            ->where('pcs.product_id', $product->id)
            ->where('sh.status', 'active')
            ->when(Schema::hasTable('orders'), function ($q) use ($openStatuses) {
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

    return view('products.show', [
        'product'               => $product,
        'variantsByColor'       => $variantsByColor,
        'reservedByVariantId'   => $reservedByVariantId,
        'availableByVariantId'  => $availableByVariantId,
        'onhandByVariantId'     => $onhandByVariantId,
        'holdsRows'             => $holdsRows,
        'openStatuses'          => $openStatuses,
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
