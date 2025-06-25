<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
   public function index(){

    $reader = Auth::user();

    $authors = Author::withCount('books')
        ->with('country')
        ->get();

    $Authors = $authors->map(function ($author) {

     return [
        'name' => $author->name,
        'id' => $author->id,
        'country_name' => $author->country?->name,
        'image' => $author->image,
        'number_of_books' =>$author->books_count ,];
        });

    return response()->json($Authors);
}}
