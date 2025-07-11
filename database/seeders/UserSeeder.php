<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin1',
                'email' => 'admin1@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'Admin2',
                'email' => 'admin2@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'Admin3',
                'email' => 'admin3@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'Manar Aljarkas',
                'email' => 'manaraljarkas@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
            [
                'name' => 'Batoul Nassar',
                'email' => 'batoulnassar@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
            [
                'name' => 'Rawan Zaaiter',
                'email' => 'rawanzaaiter@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
            [
                'name' => 'Sarrah Atiah',
                'email' => 'saraahatiah@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
            [
                'name' => 'Aya Omran',
                'email' => 'ayaomran@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
            [
                'name' => 'Enass Alkadree',
                'email' => 'enassalkadree@test.com',
                'password' => Hash::make('12345678'),
                'role' => 'reader',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'fcm_token' => null,
                'role' => $userData['role'],
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
