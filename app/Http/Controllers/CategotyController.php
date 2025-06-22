<?php

namespace App\Http\Controllers;

use App\Models\Category;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategotyController extends Controller
{
    public function index(){

    $categories=Category::select('id','name','icon')->get();
    $readerId = Auth::id();

    $categories=$categories->map(function($category) use ($readerId){
    $is_followed= DB::table('reader_categories')->
     where('reader_categories.reader_id','=',$readerId)->
      where('reader_categories.category_id','=',$category->id)->exists();

         return [
        'id' => $category->id,
        'name' => $category->name,
        'icon' => $category->icon,
        'is_followed' => $is_followed,
    ];
    });

    return response()->json($categories);


    }

}
