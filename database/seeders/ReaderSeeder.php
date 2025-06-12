<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $readers = [
            [
                'email' => 'manaraljarkas@test.com',
                'first_name' => 'Manar',
                'last_name' => 'Aljarkas',
                'nickname' => 'Manou',
            ],
            [
                'email' => 'batoulnassar@test.com',
                'first_name' => 'Batoul',
                'last_name' => 'Nassar',
                'nickname' => 'Bato',
            ],
            [
                'email' => 'rawanzaaiter@test.com',
                'first_name' => 'Rawan',
                'last_name' => 'Zaaiter',
                'nickname' => 'Roro',
            ],
            [
                'email' => 'saraahatiah@test.com',
                'first_name' => 'Sarrah',
                'last_name' => 'Atiah',
                'nickname' => 'Soso',
            ],
            [
                'email' => 'ayaomran@test.com',
                'first_name' => 'Aya',
                'last_name' => 'Omran',
                'nickname' => 'Ayosh',
            ],
            [
                'email' => 'enassalkadree@test.com',
                'first_name' => 'Enass',
                'last_name' => 'Alkadree',
                'nickname' => 'Nasso',
            ],
        ];

        foreach ($readers as $reader) {
            $user = DB::table('users')->where('email', $reader['email'])->first();

            if ($user) {
                DB::table('readers')->insert([
                    'user_id' => $user->id,
                    'points' => rand(0, 500),
                    'first_name' => $reader['first_name'],
                    'last_name' => $reader['last_name'],
                    'nickname' => $reader['nickname'],
                    'picture' => 'default.jpg',
                    'bio' => 'A passionate book lover.',
                    'quote' => 'Reading is dreaming with open eyes.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
