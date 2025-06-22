<?php

namespace App\Http\Controllers;

use App\Models\BookSuggestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
   public function index(){
     $reader = Auth::user();
    $suggestions=BookSuggestion::select('title','author_name','reader_id','created_at')->get();
    $suggestions=$suggestions->map(function($suggestion){
    $date_of_suggestion=Carbon::parse($suggestion->created_at);
     return $suggestion;
    });

    return response()->json($suggestions);

   }
}
