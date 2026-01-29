<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * แสดงรายชื่อผู้ใช้ทั้งหมด
     */
    public function index(Request $request)
    {
        $query = User::query();

        // ✅ ถ้าเป็น Sales หรือ Stock จะเห็นแค่ตัวเองเท่านั้น
        if (auth()->user()->role !== 'admin') {
            $query->where('id', auth()->id());
        }

        // ค้นหา (สำหรับ Admin)
        if ($request->filled('search') && auth()->user()->role === 'admin') {
            $query->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // กรอง Role (สำหรับ Admin)
        if ($request->filled('role') && auth()->user()->role === 'admin') {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * แสดงฟอร์มเพิ่มผู้ใช้ (เฉพาะ Admin)
     */
    public function create()
    {
        // ✅ ห้าม Sales และ Stock เข้าถึง
        if (auth()->user()->role !== 'admin') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('users.create');
    }

    /**
     * บันทึกผู้ใช้ใหม่ (เฉพาะ Admin)
     */
    public function store(Request $request)
    {
        // ✅ ห้าม Sales และ Stock เข้าถึง
        if (auth()->user()->role !== 'admin') {
            abort(403, 'คุณไม่มีสิทธิ์ทำรายการนี้');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,stock,sales',
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไข
     */
    public function edit(User $user)
    {
        // ✅ ป้องกัน: ถ้าไม่ใช่ Admin และพยายามแก้ไขคนอื่น ให้ดีดออก
        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขข้อมูลผู้อื่น');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้
     */
    public function update(Request $request, User $user)
    {
        // ✅ ป้องกัน: ถ้าไม่ใช่ Admin และพยายามแก้ไขคนอื่น
        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขข้อมูลผู้อื่น');
        }

        $rules = [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // ✅ เฉพาะ Admin เท่านั้นที่ตรวจสอบ/เปลี่ยน Role ได้
        if (auth()->user()->role === 'admin') {
            $rules['role'] = 'required|in:admin,stock,sales';
        }

        $validated = $request->validate($rules);

        $user->username = $validated['username'];
        $user->email = $validated['email'];

        // ✅ เฉพาะ Admin เท่านั้นที่เปลี่ยน Role ได้
        if (auth()->user()->role === 'admin' && isset($validated['role'])) {
            $user->role = $validated['role'];
        }
        // ❌ ถ้าไม่ใช่ Admin ระบบจะไม่บันทึก Role (ใช้ค่าเดิม)

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // ถ้าแก้ไขตัวเอง ให้กลับไปหน้า Dashboard หรือหน้าเดิม
        if (auth()->id() === $user->id && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อย');
        }

        return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    /**
     * ลบผู้ใช้ (เฉพาะ Admin)
     */
    public function destroy(User $user)
    {
        // ✅ ห้าม Sales และ Stock ลบผู้ใช้
        if (auth()->user()->role !== 'admin') {
            abort(403, 'คุณไม่มีสิทธิ์ลบผู้ใช้');
        }

        // ป้องกันไม่ให้ลบตัวเอง
        if (auth()->id() === $user->id) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }
}