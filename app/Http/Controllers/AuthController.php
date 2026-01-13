<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. แสดงหน้า Login
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // ถ้าล็อกอินอยู่แล้วให้ไปหน้าหลัก
        }
        return view('auth.login');
    }

    // 2. ตรวจสอบข้อมูล Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required', // ใช้ username ตามตารางคุณ
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // เช็คสิทธิ์เพื่อส่งไปหน้าต่างกัน (Option)
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('dashboard');
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    // 3. ระบบ Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}