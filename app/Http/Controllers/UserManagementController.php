<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * ===================================================================
 * UserManagementController - จัดการผู้ใช้งาน
 * ===================================================================
 * ฟีเจอร์:
 * 1. แสดงรายชื่อผู้ใช้ทั้งหมด (index)
 * 2. เพิ่มผู้ใช้ใหม่ (create, store)
 * 3. แก้ไขผู้ใช้ (edit, update)
 * 4. ลบผู้ใช้ (destroy) - ห้ามลบตัวเอง
 * 
 * **สิทธิ์การเข้าถึง: Admin Only**
 * ===================================================================
 */
class UserManagementController extends Controller
{
    /**
     * Constructor - ตรวจสอบสิทธิ์ Admin
     */
    public function __construct()
    {
        // ใช้ Middleware 'role:admin' จาก web.php แล้ว
        // หรือจะเพิ่มเช็คเพิ่มเติมที่นี่ก็ได้
    }

    /**
     * หน้ารายชื่อผู้ใช้ทั้งหมด
     */
    public function index(Request $request)
    {
        // ค้นหาผู้ใช้ (ถ้ามี)
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(20);

        return view('users.index', compact('users', 'search'));
    }

    /**
     * หน้าฟอร์มเพิ่มผู้ใช้ใหม่
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
        // Validate ข้อมูล
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,stock,sales',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้มีอยู่แล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            'role.required' => 'กรุณาเลือกบทบาท',
        ]);

        // สร้างผู้ใช้ใหม่
        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * หน้าฟอร์มแก้ไขผู้ใช้
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
        // Validate ข้อมูล
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,stock,sales',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้มีอยู่แล้ว',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
        ]);

        // อัปเดตข้อมูล
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // ถ้ากรอกรหัสผ่านใหม่ ให้เปลี่ยน
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * ลบผู้ใช้ (ห้ามลบตัวเอง)
     */
    public function destroy(User $user)
    {
        // ✅ ห้ามลบตัวเอง
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'ไม่สามารถลบผู้ใช้ของตัวเองได้');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }
}