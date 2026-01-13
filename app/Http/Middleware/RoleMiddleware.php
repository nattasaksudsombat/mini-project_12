<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
   public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // ถ้า User เป็น admin ให้ผ่านได้ทุกกรณี (God Mode)
        if ($user->role === 'admin') {
            return $next($request);
        }

        // เช็คว่า Role ของ User อยู่ในรายการที่อนุญาตหรือไม่
        // เช่น route กำหนด role:stock,admin -> ถ้า user เป็น stock ก็ผ่าน
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // ถ้าไม่มีสิทธิ์ ให้ดีดกลับหน้า Dashboard พร้อมแจ้งเตือน
        return redirect()->route('dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (' . $user->role . ')');
    }
}