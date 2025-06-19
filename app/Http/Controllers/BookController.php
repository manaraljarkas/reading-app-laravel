<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;

class BookController extends Controller
{
   public function getBookFile($BookId){

    $book=Book::select('book_pdf')->where('id','=',$BookId)->first();

     $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

    return response()->json(['pdf_url' => $fileUrl]);
    }

    public function getBooksComments($BookId){
    $comments=Comment::where('book_id','=',$BookId)->get();

    return response()->json(
    $comments
    );
    }
}
