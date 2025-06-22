<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\AuthorCotroller;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Support\Facades\Route;


//      Unauthenticated  routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);









//      Authenticated  routes
Route::middleware('auth:sanctum')->group(function()
{
Route::post('logout', [AuthController::class, 'logout']);
Route::post('auth/setup-profile', [AuthController::class, 'setupProfile']);
Route::post('auth/edit-profile', [AuthController::class, 'editProfile']);

Route::get('/getAuthors',[AuthorCotroller::class,'index']);
Route::get('/getCategories',[CategotyController::class,'index']);
Route::get('/getchallenges',[ChallengesController::class,'index']);
Route::get('/getBookFile/{BookId}',[BookController::class,'getBookFile']);
Route::get('/getBooksComments/{BookId}',[BookController::class,'getBooksComments']);
Route::get('/getsuggestions',[SuggestionController::class,'index']);

});
