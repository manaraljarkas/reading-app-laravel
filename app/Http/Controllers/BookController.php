<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
   public function getBookFile($BookId)
   {
    $reader = Auth::user();
    $book=Book::select('book_pdf')->where('id','=',$BookId)->first();

     $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

    return response()->json(['pdf_url' => $fileUrl]);
    }

    public function getBooksComments($BookId)
    {
        $reader = Auth::user();
        $comments=Comment::where('book_id','=',$BookId)->get();

        return response()->json(
        $comments
        );
    }

    public function getNumbers()
    {
        $reader=Auth::user();

        $readers=Reader::Count();
        $books=Book::Count();
        $challenges=Challenge::Count();
        $categories=Category::Count();
        $admins=User::where('role','=','admin')->count();

        return response()->json([
        'number_of_readers'=>$readers,
        'number_of_books'=>$books,
        'number_of_challenges'=>$challenges,
        'number_of_categories'=>$categories,
        'number_of_admins'=>$admins
        ]);

    }
}
