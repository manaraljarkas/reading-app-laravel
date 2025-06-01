<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'fcm_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'role' => $user['role'] ?? null,
            ]);
        }
    }
}
