<?php

namespace App\Http\Controllers;

use App\Models\Reader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReaderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $readers = Reader::select('id', 'first_name', 'picture', 'user_id')
            ->with('user')
            ->paginate(10)
            ->through(function ($reader) {
                return [
                    'id' => $reader->id,
                    'name' => $reader->first_name,
                    'email' => $reader->user?->email ?? 'no user attached',
                    'picture' => asset('storage/images/readers/' . $reader->picture),
                ];
            });
        return response()->json($readers);
    }

    public function show($readerId)
    {
        $reader = Auth::user();
        $readerinfo = Reader::select('first_name', 'picture', 'points', 'bio', 'nickname')->where('id', '=', $readerId)->first();

        $number_of_challenges = DB::table('reader_challenges')->where('reader_challenges.reader_id', '=', $readerId)->count();
        $number_of_books = DB::table('reader_books')->where('reader_books.reader_id', '=', $readerId)->count();
        $number_of_countries = DB::table('reader_books')->join('books', 'reader_books.book_id', '=', 'books.id')->join('authors', 'authors.id', '=', 'books.author_id')->distinct('authors.country_id')->count('authors.country_id');

        $number_of_badges = DB::table('reader_badges')->where('reader_badges.reader_id', '=', $readerId)->count();

        return response()->json([
            'name' => $readerinfo->first_name,
            'picture' => $readerinfo->picture ? asset('storage/images/readers/' . $readerinfo->picture) : null,
            'nickname' => $readerinfo->nickname,
            'bio' => $readerinfo->bio,
            'number_of_challenges' => $number_of_challenges,
            'number_of_books' => $number_of_books,
            'points' => $readerinfo->points,
            'number_of_countries' => $number_of_countries,
            'number_of_badges' => $number_of_badges,
        ]);
    }

    public function destroy($readerId)
    {
        $user = Auth::user();
        $reader = Reader::find($readerId);

        if (!$reader) {
            return response()->json(['message' => 'Reader not found'], 404);
        }
        $reader->delete();
        return response()->json(['message' => 'Reader deleted successfully']);
    }
}
