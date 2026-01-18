<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all users except current admin (optional: or show all)
        $users = User::orderBy('id')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,sales,stock',
        ], [
            'username.unique' => 'ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว',
            'email.unique' => 'อีเมลนี้มีอยู่ในระบบแล้ว',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            'role.in' => 'บทบาทไม่ถูกต้อง'
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not implemented
        return redirect()->route('users.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,sales,stock',
        ]);

        $data = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้งานสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตนเองได้');
        }

        // Prevent deleting the last admin? (Optional safety check)
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'ไม่สามารถลบผู้ดูแลระบบคนสุดท้ายได้');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานสำเร็จ');
    }
}
