<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin-01',
            'email' => 'admin@cloudex.com',
            'password' => Hash::make('Yang@Berkuasa_123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'bio' => 'System Administrator'
        ]);

        User::create([
            'name' => 'Co-Admin-01',
            'email' => 'coadmin@cloudex.com',
            'password' => Hash::make('Dibawah@Raja_123'),
            'role' => 'coadmin',
            'phone' => '081234567891',
            'bio' => 'Co-Administrator'
        ]);

        User::create([
            'name' => 'Mahdi',
            'email' => 'mahdi@user.com',
            'password' => Hash::make('Mahdi@Saja_123'),
            'role' => 'user',
            'phone' => '081234567892',
            'bio' => 'Regular User'
        ]);
    }
}