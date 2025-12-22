<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    /**
     * รายการลูกค้า + ค้นหา + withCount('orders')
     * รองรับค้นหา: ชื่อ / เบอร์โทร / ที่อยู่ (ค้นจาก customer_addresses ถ้ามีตาราง)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // ใช้ Eloquent + withCount('orders') ตามที่ขอ
        $builder = Customer::query()->withCount('orders')->orderByDesc('id');

        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $like = '%'.$q.'%';
                $w->where('name', 'like', $like)
                  ->orWhere('phone', 'like', $like);
            });

            // ค้นที่อยู่จากตาราง customer_addresses ถ้ามี
            if (Schema::hasTable('customer_addresses')) {
                $builder->orWhereHas('addresses', function ($aw) use ($q) {
                    $like = '%'.$q.'%';
                    $aw->where(function ($x) use ($like) {
                        $x->where('name', 'like', $like)
                          ->orWhere('address', 'like', $like)
                          ->orWhere('district', 'like', $like)
                          ->orWhere('province', 'like', $like)
                          ->orWhere('postal_code', 'like', $like);
                    });
                });
            } else {
                // fallback: ค้นใน customers.address ถ้ามีคอลัมน์
                if (Schema::hasColumn('customers', 'address')) {
                    $builder->orWhere('address', 'like', '%'.$q.'%');
                }
            }
        }

        $customers = $builder->paginate(20)->appends(['q' => $q]);

        // NOTE: คุณต้องมี view: resources/views/customers/index.blade.php
        // (ตอนนี้คืนค่าตัวแปร $customers และ $q ไปให้)
        return view('customers.index', compact('customers', 'q'));
    }

    /**
     * หน้าเพิ่มลูกค้า
     * (เตรียมฟอร์ม + ไม่ทำงาน DB ใดๆ)
     */
    public function create()
    {
        // NOTE: คุณต้องมี view: resources/views/customers/create.blade.php
        return view('customers.create');
    }

    /**
     * บันทึกลูกค้าใหม่ + รองรับ addresses[] แบบหลายรายการในครั้งเดียว (ใช้ Transaction)
     * ฟอร์แมตคาดหวัง:
     * - name, phone, purchase_channel, payment_method (ของลูกค้า)
     * - addresses: [
     *      { name, address, district, province, postal_code },
     *      ...
     *   ]
     */
    public function store(Request $request)
    {
        // วาลิเดชันแบบยืดหยุ่น (ไม่ทำลายของเดิม)
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'purchase_channel' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:50',

            // ที่อยู่หลายรายการ (ถ้ามี)
            'addresses'                    => 'nullable|array',
            'addresses.*.name'             => 'nullable|string|max:100',
            'addresses.*.address'          => 'nullable|string|max:500',
            'addresses.*.district'         => 'nullable|string|max:100',
            'addresses.*.province'         => 'nullable|string|max:100',
            'addresses.*.postal_code'      => 'nullable|string|max:20',

            // เผื่อฟอร์มเก่า: customer[address] เดี่ยว
            'address'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {

            // 1) สร้างลูกค้า
            $customer = Customer::create([
                'name'             => $request->input('name'),
                'phone'            => $request->input('phone'),
                'purchase_channel' => $request->input('purchase_channel'),
                'payment_method'   => $request->input('payment_method'),
                // **อย่าลบ** address เดี่ยว — เผื่อของเดิมยังอ้างอยู่
                'address'          => $request->input('address', ''), 
            ]);

            // 2) ถ้ามีตาราง customer_addresses และมี addresses[] ให้บันทึกทั้งหมด
            if (Schema::hasTable('customer_addresses')) {
                $addresses = $this->sanitizeAddressesArray($request->input('addresses', []));

                if (!empty($addresses)) {
                    foreach ($addresses as $a) {
                        CustomerAddress::create([
                            'customer_id' => $customer->id,
                            'name'        => $a['name'] ?? null,
                            'address'     => $a['address'] ?? null,
                            'district'    => $a['district'] ?? null,
                            'province'    => $a['province'] ?? null,
                            'postal_code' => $a['postal_code'] ?? null,
                        ]);
                    }
                } elseif ($customer->address) {
                    // fallback: ถ้าไม่มี addresses[] แต่มี address เดี่ยว → สร้างรายการ default ให้ 1 อัน
                    CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'name'        => 'ที่อยู่หลัก',
                        'address'     => $customer->address,
                        'district'    => $request->input('district'),  // ถ้ามีในฟอร์ม
                        'province'    => $request->input('province'),
                        'postal_code' => $request->input('postal_code'),
                    ]);
                }
            }

            return redirect()->route('customers.index')->with('success', 'บันทึกลูกค้าเรียบร้อย');
        });
    }

    /**
     * หน้าแก้ไขลูกค้า
     */
    public function edit(Customer $customer)
    {
        // ดึง addresses ถ้ามีตาราง
        $addresses = collect();
        if (Schema::hasTable('customer_addresses')) {
            $addresses = $customer->addresses()->orderBy('id')->get();
        }

        // NOTE: คุณต้องมี view: resources/views/customers/edit.blade.php
        return view('customers.edit', compact('customer', 'addresses'));
    }

    /**
     * อัปเดตลูกค้า + sync addresses (เพิ่ม/แก้/ลบ) ด้วย Transaction
     * - ถ้า addresses[].id มีค่า → update แถวนั้น
     * - ถ้าไม่มี id → create ใหม่
     * - ลบแถวที่ไม่อยู่ในรายการที่ส่งมา
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'purchase_channel' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:50',

            'addresses'                    => 'nullable|array',
            'addresses.*.id'               => 'nullable|integer',
            'addresses.*.name'             => 'nullable|string|max:100',
            'addresses.*.address'          => 'nullable|string|max:500',
            'addresses.*.district'         => 'nullable|string|max:100',
            'addresses.*.province'         => 'nullable|string|max:100',
            'addresses.*.postal_code'      => 'nullable|string|max:20',

            'address'          => 'nullable|string|max:500', // เผื่อของเดิม
        ]);

        return DB::transaction(function () use ($request, $customer) {

            // 1) อัปเดตข้อมูลลูกค้า
            $customer->update([
                'name'             => $request->input('name'),
                'phone'            => $request->input('phone'),
                'purchase_channel' => $request->input('purchase_channel'),
                'payment_method'   => $request->input('payment_method'),
                'address'          => $request->input('address', $customer->address), // อย่าทิ้งของเดิม
            ]);

            // 2) จัดการ addresses ถ้ามีตาราง
            if (Schema::hasTable('customer_addresses')) {
                $payload = $this->sanitizeAddressesArray($request->input('addresses', []));
                $keepIds = [];

                foreach ($payload as $a) {
                    $rowId = isset($a['id']) ? (int) $a['id'] : 0;

                    // อัปเดตของเดิม
                    if ($rowId > 0) {
                        /** @var CustomerAddress|null $row */
                        $row = $customer->addresses()->where('id', $rowId)->first();
                        if ($row) {
                            $row->update([
                                'name'        => $a['name'] ?? null,
                                'address'     => $a['address'] ?? null,
                                'district'    => $a['district'] ?? null,
                                'province'    => $a['province'] ?? null,
                                'postal_code' => $a['postal_code'] ?? null,
                            ]);
                            $keepIds[] = $row->id;
                            continue;
                        }
                    }

                    // สร้างใหม่
                    $new = $customer->addresses()->create([
                        'name'        => $a['name'] ?? null,
                        'address'     => $a['address'] ?? null,
                        'district'    => $a['district'] ?? null,
                        'province'    => $a['province'] ?? null,
                        'postal_code' => $a['postal_code'] ?? null,
                    ]);
                    $keepIds[] = $new->id;
                }

                // ลบที่อยู่ที่ไม่อยู่ในรายการ (sync)
                if (!empty($keepIds)) {
                    $customer->addresses()->whereNotIn('id', $keepIds)->delete();
                } else {
                    // ถ้าผู้ใช้ลบทุกแถว → ลบทั้งหมด
                    $customer->addresses()->delete();
                }

                // fallback: ถ้าไม่มีแถวเลย และยังมี customers.address → ยัด default 1 แถว
                if ($customer->addresses()->count() === 0 && $customer->address) {
                    $customer->addresses()->create([
                        'name'        => 'ที่อยู่หลัก',
                        'address'     => $customer->address,
                        'district'    => $request->input('district'),
                        'province'    => $request->input('province'),
                        'postal_code' => $request->input('postal_code'),
                    ]);
                }
            }

            return redirect()->route('customers.index')->with('success', 'อัปเดตข้อมูลลูกค้าเรียบร้อย');
        });
    }

    /**
     * ลบลูกค้าอย่างปลอดภัย
     * - ถ้ามี orders อยู่ → แนะนำให้บล็อก (กันพังความสัมพันธ์)
     *   (ถ้าอยากบังคับลบ ให้แก้ตามนโยบายของคุณ)
     */
    public function destroy(Customer $customer)
    {
        return DB::transaction(function () use ($customer) {

            // กันพัง: ถ้ามีออเดอร์อยู่ ไม่ให้ลบ (แก้ตามนโยบายได้)
            if (method_exists($customer, 'orders') && $customer->orders()->count() > 0) {
                return back()->with('error', 'ไม่สามารถลบลูกค้าที่มีออเดอร์ได้');
            }

            // ลบ addresses ถ้ามีตาราง
            if (Schema::hasTable('customer_addresses')) {
                $customer->addresses()->delete();
            }

            $customer->delete();

            return redirect()->route('customers.index')->with('success', 'ลบลูกค้าเรียบร้อย');
        });
    }

    /* ===================== Helpers ===================== */

    /**
     * ทำความสะอาด payload addresses[]
     */
    private function sanitizeAddressesArray($arr): array
    {
        if (!is_array($arr)) return [];

        $clean = [];
        foreach ($arr as $a) {
            if (!is_array($a)) continue;

            $row = [
                'id'          => isset($a['id']) ? (int) $a['id'] : null,
                'name'        => $this->t($a['name'] ?? null),
                'address'     => $this->t($a['address'] ?? null),
                'district'    => $this->t($a['district'] ?? null),
                'province'    => $this->t($a['province'] ?? null),
                'postal_code' => $this->t($a['postal_code'] ?? null),
            ];

            // ข้ามแถวที่ว่างจริง ๆ
            if ($row['name'] || $row['address'] || $row['district'] || $row['province'] || $row['postal_code']) {
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
