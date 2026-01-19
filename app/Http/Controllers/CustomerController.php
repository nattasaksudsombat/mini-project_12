<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $builder = Customer::query()->withCount('orders')->orderByDesc('id');

        // ✅ ค้นหาจากชื่อ, เบอร์โทร, อีเมล
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $builder->where(function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where('name', 'like', $like)
                      ->orWhere('phone', 'like', $like)
                      ->orWhere('email', 'like', $like);
            });

            // ✅ ค้นหาจากที่อยู่ (ถ้ามีตาราง customer_addresses)
            if (Schema::hasTable('customer_addresses')) {
                $builder->orWhereHas('addresses', function ($addressQuery) use ($search) {
                    $like = '%' . $search . '%';
                    $addressQuery->where(function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                          ->orWhere('address', 'like', $like)
                          ->orWhere('subdistrict', 'like', $like)
                          ->orWhere('district', 'like', $like)
                          ->orWhere('province', 'like', $like)
                          ->orWhere('postal_code', 'like', $like);
                    });
                });
            }
        }

        // ✅ กรองตามช่องทางการซื้อ
        if ($request->filled('purchase_channel')) {
            $builder->where('purchase_channel', $request->input('purchase_channel'));
        }

        // ✅ กรองตามวิธีชำระเงิน
        if ($request->filled('payment_method')) {
            $builder->where('payment_method', $request->input('payment_method'));
        }

        $customers = $builder->paginate(20)->appends($request->query());
        
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        // 1. Validate ครบทุกช่อง
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'purchase_channel' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:50',
            'notes'            => 'nullable|string',

            'addresses'               => 'nullable|array',
            'addresses.*.name'        => 'nullable|string|max:100',
            'addresses.*.address'     => 'nullable|string|max:500',
            'addresses.*.soi'         => 'nullable|string|max:100',
            'addresses.*.road'        => 'nullable|string|max:100',
            'addresses.*.subdistrict' => 'nullable|string|max:100',
            'addresses.*.district'    => 'nullable|string|max:100',
            'addresses.*.province'    => 'nullable|string|max:100',
            'addresses.*.postal_code' => 'nullable|string|max:20',

            'address'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            // สร้างลูกค้า
            $customer = Customer::create([
                'name'             => $request->input('name'),
                'phone'            => $request->input('phone'),
                'email'            => $request->input('email'),
                'purchase_channel' => $request->input('purchase_channel'),
                'payment_method'   => $request->input('payment_method'),
                'notes'            => $request->input('notes'),
                'address'          => $request->input('address', ''), 
            ]);

            // บันทึกที่อยู่
            if (Schema::hasTable('customer_addresses')) {
                $addresses = $this->sanitizeAddressesArray($request->input('addresses', []));

                if (!empty($addresses)) {
                    foreach ($addresses as $a) {
                        CustomerAddress::create([
                            'customer_id' => $customer->id,
                            'name'        => $a['name'] ?? '',
                            'address'     => $a['address'] ?? '',
                            'soi'         => $a['soi'] ?? '',
                            'road'        => $a['road'] ?? '',
                            'subdistrict' => $a['subdistrict'] ?? '', // ✅ ใส่ค่า default เป็น ''
                            'district'    => $a['district'] ?? '',
                            'province'    => $a['province'] ?? '',
                            'postal_code' => $a['postal_code'] ?? '',
                        ]);
                    }
                } elseif ($customer->address) {
                    CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'name'        => 'ที่อยู่หลัก',
                        'address'     => $customer->address,
                        'soi'         => '',
                        'road'        => '',
                        'subdistrict' => $request->input('subdistrict', ''),
                        'district'    => $request->input('district', ''),
                        'province'    => $request->input('province', ''),
                        'postal_code' => $request->input('postal_code', ''),
                    ]);
                }
            }

            return redirect()->route('customers.index')->with('success', 'บันทึกลูกค้าเรียบร้อย');
        });
    }

    public function edit(Customer $customer)
    {
        $addresses = collect();
        if (Schema::hasTable('customer_addresses')) {
            $addresses = $customer->addresses()->orderBy('id')->get();
        }
        return view('customers.edit', compact('customer', 'addresses'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'purchase_channel' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:50',
            'notes'            => 'nullable|string',

            'addresses'               => 'nullable|array',
            'addresses.*.id'          => 'nullable|integer',
            'addresses.*.name'        => 'nullable|string|max:100',
            'addresses.*.address'     => 'nullable|string|max:500',
            'addresses.*.soi'         => 'nullable|string|max:100',
            'addresses.*.road'        => 'nullable|string|max:100',
            'addresses.*.subdistrict' => 'nullable|string|max:100',
            'addresses.*.district'    => 'nullable|string|max:100',
            'addresses.*.province'    => 'nullable|string|max:100',
            'addresses.*.postal_code' => 'nullable|string|max:20',

            'address'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $customer) {
            $customer->update([
                'name'             => $request->input('name'),
                'phone'            => $request->input('phone'),
                'email'            => $request->input('email'),
                'purchase_channel' => $request->input('purchase_channel'),
                'payment_method'   => $request->input('payment_method'),
                'notes'            => $request->input('notes'),
                'address'          => $request->input('address', $customer->address),
            ]);

            if (Schema::hasTable('customer_addresses')) {
                $payload = $this->sanitizeAddressesArray($request->input('addresses', []));
                $keepIds = [];

                foreach ($payload as $a) {
                    $rowId = isset($a['id']) ? (int) $a['id'] : 0;
                    
                    // เตรียม data array (ใส่ default '' แทน null)
                    $dataToSave = [
                        'name'        => $a['name'] ?? '',
                        'address'     => $a['address'] ?? '',
                        'soi'         => $a['soi'] ?? '',
                        'road'        => $a['road'] ?? '',
                        'subdistrict' => $a['subdistrict'] ?? '', // ✅ ใส่ค่า default
                        'district'    => $a['district'] ?? '',
                        'province'    => $a['province'] ?? '',
                        'postal_code' => $a['postal_code'] ?? '',
                    ];

                    if ($rowId > 0) {
                        $row = $customer->addresses()->where('id', $rowId)->first();
                        if ($row) {
                            $row->update($dataToSave);
                            $keepIds[] = $row->id;
                            continue;
                        }
                    }

                    $new = $customer->addresses()->create($dataToSave);
                    $keepIds[] = $new->id;
                }

                if (!empty($keepIds)) {
                    $customer->addresses()->whereNotIn('id', $keepIds)->delete();
                } else {
                    $customer->addresses()->delete();
                }
            }

            return redirect()->route('customers.index')->with('success', 'อัปเดตข้อมูลลูกค้าเรียบร้อย');
        });
    }

    public function destroy(Customer $customer)
    {
        return DB::transaction(function () use ($customer) {
            if (method_exists($customer, 'orders') && $customer->orders()->count() > 0) {
                return back()->with('error', 'ไม่สามารถลบลูกค้าที่มีออเดอร์ได้');
            }
            if (Schema::hasTable('customer_addresses')) {
                $customer->addresses()->delete();
            }
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'ลบลูกค้าเรียบร้อย');
        });
    }

    private function sanitizeAddressesArray($arr): array
    {
        if (!is_array($arr)) return [];
        $clean = [];
        foreach ($arr as $a) {
            if (!is_array($a)) continue;
            $row = [
                'id'          => isset($a['id']) ? (int) $a['id'] : null,
                'name'        => $this->t($a['name'] ?? null) ?? '',        // ✅ ใส่ default ''
                'address'     => $this->t($a['address'] ?? null) ?? '',     // ✅
                'soi'         => $this->t($a['soi'] ?? null) ?? '',         // ✅
                'road'        => $this->t($a['road'] ?? null) ?? '',        // ✅
                'subdistrict' => $this->t($a['subdistrict'] ?? null) ?? '', // ✅ สำคัญที่สุด!
                'district'    => $this->t($a['district'] ?? null) ?? '',    // ✅
                'province'    => $this->t($a['province'] ?? null) ?? '',    // ✅
                'postal_code' => $this->t($a['postal_code'] ?? null) ?? '', // ✅
            ];
            // เช็คว่ามีข้อมูลบ้างไหม (อย่างน้อย 1 field)
            if ($row['address'] || $row['subdistrict'] || $row['district'] || $row['province']) {
                $clean[] = $row;
            }
        }
        return $clean;
    }

    private function t($v): ?string
    {
        if ($v === null) return null;
        $s = trim((string) $v);
        return $s === '' ? null : $s;
    }
}