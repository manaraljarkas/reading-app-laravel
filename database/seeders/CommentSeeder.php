<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $comments=[
            [
                'comment' => 'A very deep and emotional book.',
                'book_id' => 5,
                'reader_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'comment' => 'Loved the plot and characters!',
                'book_id' => 5,
                'reader_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'comment' => 'Not my type, but well-written.',
                'book_id' => 5,
                'reader_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
       ];
      foreach ($comments as $comment) {
            DB::table('comments')->insert([
               'comment' => $comment['comment'],
                'book_id' => $comment['book_id'],
                'reader_id' => $comment['reader_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }
}
