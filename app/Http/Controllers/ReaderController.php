<?php

namespace App\Http\Controllers;

use App\Models\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReaderController extends Controller
{
    public function getReaderInfo($readerId){

    $reader=Auth::user();
    $readerinfo=Reader::select('first_name','picture','points','bio','nickname')->
    where('id','=',$readerId)->first();

    $number_of_challenges=DB::table('reader_challenges')->
    where('reader_challenges.reader_id','=',$readerId)->
    count();
    $number_of_books=DB::table('reader_books')->
    where('reader_books.reader_id','=',$readerId)->count();
    $number_of_countries=DB::table('reader_books')->
    join('books','reader_books.book_id','=','books.id')->
    join('authors','authors.id','=','books.author_id')->
    distinct('authors.country_id')->
    count('authors.country_id');

    $number_of_badges=DB::table('reader_badges')->
    where('reader_badges.reader_id','=',$readerId)->
    count();

    return response()->json([
    'first_name'=>$readerinfo->first_name,
    'points'=>$readerinfo->points,
    'bio'=>$readerinfo->bio,
    'nickname'=>$readerinfo->nickname,
    'picture'=> $readerinfo->picture ? asset('storage/' . $readerinfo->picture): null,
    'number_of_challenges'=>$number_of_challenges,
    'number_of_books'=>$number_of_books,
    'number_of_countries'=>$number_of_countries,
    'number_of_badges'=>$number_of_badges
    ]);
    }

    public function DeleteReader($readerId){
    $reader=Auth::user();
    $delete=Reader::find($readerId)->delete();
    return response()->json([
    'message'=>'reader deleted successfuly'
   ]);
    }

    
}
