<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserEditPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->route('user');
        
        // Admin แก้ไขใครก็ได้
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }
        
        // Sales/Stock แก้ไขแค่ตัวเอง
        if (auth()->id() !== $user->id) {
            abort(403, 'คุณสามารถแก้ไขได้เฉพาะข้อมูลของตัวเองเท่านั้น');
        }
        
        return $next($request);
    }
}