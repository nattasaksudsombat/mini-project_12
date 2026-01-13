<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    public function run()
    {
        
        // 1. Admin (มีอยู่แล้วหรือสร้างใหม่)
        User::updateOrCreate(
            ['username' => 'admin'], // เช็คจาก username
            [
                'email' => 'admin@shop.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin'
            ]
        );

        // 2. Stock (พนักงานสต๊อก)
        User::updateOrCreate(
            ['username' => 'stock'],
            [
                'email' => 'stock@shop.com',
                'password' => Hash::make('12345678'),
                'role' => 'stock'
            ]
        );

        // 3. Sales (พนักงานขาย)
        User::updateOrCreate(
            ['username' => 'sales'],
            [
                'email' => 'sales@shop.com',
                'password' => Hash::make('12345678'),
                'role' => 'sales'
            ]
        );
        
        // 4. Sales 2
        User::updateOrCreate(
            ['username' => 'sales2'],
            [
                'email' => 'sales2@shop.com',
                'password' => Hash::make('12345678'),
                'role' => 'sales'
            ]
        );
    }
}