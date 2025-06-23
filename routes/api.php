<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorCotroller;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\complaintController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;





//      Unauthenticated  routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/dashboard/login', [AuthController::class, 'webLogin']);









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

Route::get('/getNumbers',[BookController::class,'getNumbers']);

Route::get('/getComplaints',[complaintController::class,'getComplaints']);

Route::post('/UpdateSuggestion/{suggestionId}',[SuggestionController::class,'UpdateSuggestion']);

Route::get('/getSuggestionInfo/{suggestionId}',[SuggestionController::class,'getSuggestionInfo']);

Route::get('/getAdminInfo/{adminId}',[UserController::class,'getAdminInfo']);

Route::post('/AddAdmin',[UserController::class,'AddAdmin']);

Route::delete('/deleteAdmin/{Adminid}',[UserController::class,'deleteAdmin']);

Route::get('/getReaderInfo/{readerId}',[ReaderController::class,'getReaderInfo']);

Route::delete('/DeleteReader/{readerId}',[ReaderController::class,'DeleteReader']);

});
