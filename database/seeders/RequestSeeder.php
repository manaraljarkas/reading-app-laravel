<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Request new book category',
            'Report issue with challenge',
            'Suggest new feature',
            'Feedback on reading experience',
            'Request for badge explanation',
            'Book recommendation',
        ];

        $readerIds = DB::table('readers')->pluck('id');

        foreach ($readerIds as $index => $readerId) {
            DB::table('requests')->insert([
                'subject' => $subjects[$index % count($subjects)],
                'description' => 'This is a request from the reader for assistance or feedback.',
                'reader_id' => $readerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
