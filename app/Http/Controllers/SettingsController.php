<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ===================================================================
 * SettingsController - ตั้งค่าทั่วไปของระบบ
 * ===================================================================
 * ฟีเจอร์:
 * 1. ตั้งค่าชื่อร้าน, เบอร์โทร, ที่อยู่ร้าน
 * 2. ตั้งค่า Low Stock Threshold (แจ้งเตือนสต็อกต่ำ)
 * 3. เก็บค่าใน table `settings` (key-value)
 * ===================================================================
 */
class SettingsController extends Controller
{
    /**
     * หน้าฟอร์มตั้งค่า
     */
    public function index()
    {
        // ดึงค่าทั้งหมดจากตาราง settings
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        // ค่า Default ถ้ายังไม่มีในฐานข้อมูล
        $defaults = [
            'shop_name' => 'ร้านค้าของฉัน',
            'shop_phone' => '02-xxx-xxxx',
            'shop_address' => 'กรุณากรอกที่อยู่ร้าน',
            'low_stock_threshold' => 10,
            'default_shipping_fee' => 50,
            'bank_accounts' => '[]',
        ];

        // Merge ค่า Default กับค่าจากฐานข้อมูล
        $settings = array_merge($defaults, $settings);

        // แปลง bank_accounts จาก JSON เป็น Array
        $settings['bank_accounts'] = json_decode($settings['bank_accounts'], true) ?? [];

        return view('settings.index', compact('settings'));
    }

    /**
     * บันทึกค่าตั้งค่า
     */
    public function update(Request $request)
    {
        // Validate ข้อมูล
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_phone' => 'nullable|string|max:20',
            'shop_address' => 'nullable|string|max:500',
            'low_stock_threshold' => 'required|integer|min:0|max:1000',
        ], [
            'shop_name.required' => 'กรุณากรอกชื่อร้าน',
            'low_stock_threshold.required' => 'กรุณากรอกค่าแจ้งเตือนสต็อก',
            'low_stock_threshold.integer' => 'ค่าต้องเป็นตัวเลข',
        ]);

        // บันทึกลงฐานข้อมูล (Update หรือ Insert)
        $this->setSetting('shop_name', $request->input('shop_name'));
        $this->setSetting('shop_phone', $request->input('shop_phone'));
        $this->setSetting('shop_address', $request->input('shop_address'));
        $this->setSetting('low_stock_threshold', $request->input('low_stock_threshold'));

        return redirect()->route('settings.index')->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    /**
     * Helper: บันทึกค่าลง settings (updateOrInsert)
     */
    private function setSetting(string $key, $value)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => (string)$value,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Helper: ดึงค่าจาก settings
     */
    public static function getSetting(string $key, $default = null)
    {
        $setting = DB::table('settings')->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}