<?php

use App\Http\Controllers\AuthorCotroller;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;






Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getchallenges/{readerId}',[ChallengesController::class,'index']);
Route::get('/getAuthors',[AuthorCotroller::class,'index']);
Route::get('/getCategories/{readerId}',[CategotyController::class,'index']);

Route::get('/getBookFile/{BookId}',[BookController::class,'getBookFile']);
Route::get('/getBooksComments/{BookId}',[BookController::class,'getBooksComments']);
Route::get('/getsuggestions',[SuggestionController::class,'index']);
