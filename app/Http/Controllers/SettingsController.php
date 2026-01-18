<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Handle file upload (Logo)
        if ($request->hasFile('shop_logo')) {
            $request->validate([
                'shop_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $path = $request->file('shop_logo')->store('settings', 'public');
            Setting::setValue('shop_logo', $path);

            // Remove from data to avoid overwriting with file object
            unset($data['shop_logo']);
        }

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
