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

        // ค้นหา
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // กรอง Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * แสดงฟอร์มเพิ่มผู้ใช้
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * บันทึกผู้ใช้ใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,stock,sales', // กำหนด Role ที่อนุญาต
        ]);

        User::create([
            'name' => $validated['name'],
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
        return view('users.edit', compact('user'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,stock,sales',
            'password' => 'nullable|string|min:8|confirmed', // Password เป็น Optional
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลผู้ใช้เรียบร้อย');
    }

    /**
     * ลบผู้ใช้
     */
    public function destroy(User $user)
    {
        // ป้องกันไม่ให้ลบตัวเอง
        if (auth()->id() === $user->id) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }
}