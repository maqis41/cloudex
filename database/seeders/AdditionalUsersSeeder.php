<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdditionalUsersSeeder extends Seeder
{
    public function run()
    {
        // 1 Admin tambahan
        User::create([
            'name' => 'Admin-02',
            'email' => 'admin02@cloudex.com',
            'password' => Hash::make('Admin02@Secure_123'),
            'role' => 'admin',
            'phone' => '081234567893',
            'bio' => 'Secondary System Administrator'
        ]);

        // 5 Co-Admin
        $coadmins = [
            [
                'name' => 'Co-Admin-02',
                'email' => 'coadmin02@cloudex.com',
                'phone' => '081234567894',
                'bio' => 'Co-Administrator 02'
            ],
            [
                'name' => 'Co-Admin-03', 
                'email' => 'coadmin03@cloudex.com',
                'phone' => '081234567895',
                'bio' => 'Co-Administrator 03'
            ],
            [
                'name' => 'Co-Admin-04',
                'email' => 'coadmin04@cloudex.com',
                'phone' => '081234567896',
                'bio' => 'Co-Administrator 04'
            ],
            [
                'name' => 'Co-Admin-05',
                'email' => 'coadmin05@cloudex.com',
                'phone' => '081234567897',
                'bio' => 'Co-Administrator 05'
            ],
            [
                'name' => 'Co-Admin-06',
                'email' => 'coadmin06@cloudex.com',
                'phone' => '081234567898',
                'bio' => 'Co-Administrator 06'
            ]
        ];

        foreach ($coadmins as $coadmin) {
            User::create([
                'name' => $coadmin['name'],
                'email' => $coadmin['email'],
                'password' => Hash::make('CoAdmin' . substr($coadmin['name'], -2) . '@Secure_123'),
                'role' => 'coadmin',
                'phone' => $coadmin['phone'],
                'bio' => $coadmin['bio']
            ]);
        }

        // 10 User biasa (9 acak + 1 test)
        $regularUsers = [
            [
                'name' => 'test',
                'email' => 'test@user.com',
                'phone' => '081234567899',
                'bio' => 'Test User Account'
            ],
            [
                'name' => 'Ahmad Santoso',
                'email' => 'ahmad.santoso@user.com',
                'phone' => '081234567900',
                'bio' => 'Software Developer'
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@user.com', 
                'phone' => '081234567901',
                'bio' => 'Graphic Designer'
            ],
            [
                'name' => 'Budi Prasetyo',
                'email' => 'budi.prasetyo@user.com',
                'phone' => '081234567902',
                'bio' => 'Project Manager'
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@user.com',
                'phone' => '081234567903',
                'bio' => 'Content Writer'
            ],
            [
                'name' => 'Rizki Abdullah',
                'email' => 'rizki.abdullah@user.com',
                'phone' => '081234567904',
                'bio' => 'Data Analyst'
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@user.com',
                'phone' => '081234567905',
                'bio' => 'Marketing Specialist'
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@user.com',
                'phone' => '081234567906',
                'bio' => 'Sales Executive'
            ],
            [
                'name' => 'Linda Permata',
                'email' => 'linda.permata@user.com',
                'phone' => '081234567907',
                'bio' => 'HR Manager'
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@user.com',
                'phone' => '081234567908',
                'bio' => 'IT Support'
            ]
        ];

        foreach ($regularUsers as $user) {
            // Generate password dari nama (lowercase + angka)
            $password = strtolower(str_replace(' ', '', $user['name'])) . '123';
            
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($password),
                'role' => 'user',
                'phone' => $user['phone'],
                'bio' => $user['bio']
            ]);
        }

        $this->command->info('Successfully added 16 new users: 1 Admin, 5 Co-Admins, and 10 Regular Users!');
        $this->command->info('Admin-02 password: Admin02@Secure_123');
        $this->command->info('Co-Admins password format: CoAdmin[number]@Secure_123');
        $this->command->info('Regular users password: username123 (lowercase, no spaces)');
    }
}