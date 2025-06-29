<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\SizeCategoryController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//      Unauthenticated  routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/dashboard/login', [AuthController::class, 'webLogin']);





//---------------------Authenticated  routes---------------------------
Route::middleware('auth:sanctum')->group(function()
{
//--------------------------Auth--------------------------
Route::post('logout', [AuthController::class, 'logout']);
Route::post('auth/setup-profile', [AuthController::class, 'setupProfile']);
Route::post('auth/edit-profile', [AuthController::class, 'editProfile']);

//--------------------------Author------------------------
Route::get('/getAuthors',[AuthorController::class,'index']);

//---------------------------Book--------------------------
Route::get('book/getBookFile/{BookId}',[BookController::class,'getBookFile']);
Route::get('book/getBooksComments/{BookId}',[BookController::class,'getBooksComments']);
Route::get('book/getNumbers',[BookController::class,'getNumbers']);
Route::get('book/getMostRatedBooks', [BookController::class, 'getMostRatedBooks']);
Route::get('book/getAuthorBooks/{authorId}', [BookController::class, 'getAuthorBooks']);
Route::get('book/getCategoryBooks/{categoryId}', [BookController::class, 'getCategoryBooks']);

//----------------------------Category----------------------------
Route::get('/getCategories',[CategotyController::class,'index']);

//----------------------------Challenge--------------------------------
Route::get('/getchallenges',[ChallengesController::class,'index']);

//----------------------------Suggestion----------------------------
Route::get('/getsuggestions',[SuggestionController::class,'index']);
Route::post('/UpdateSuggestion/{suggestionId}',[SuggestionController::class,'UpdateSuggestion']);
Route::get('/getSuggestionInfo/{suggestionId}',[SuggestionController::class,'getSuggestionInfo']);

//----------------------------Complaint-------------------------------
Route::get('/getComplaints',[ComplaintController::class,'getComplaints']);

//----------------------------Admin--------------------------------------
Route::get('/getAdminInfo/{adminId}',[UserController::class,'getAdminInfo']);
Route::post('/AddAdmin',[UserController::class,'AddAdmin']);
Route::delete('/deleteAdmin/{Adminid}',[UserController::class,'deleteAdmin']);

//-----------------------------Reader-------------------------------------
Route::get('/getReaderInfo/{readerId}',[ReaderController::class,'getReaderInfo']);
Route::delete('/DeleteReader/{readerId}',[ReaderController::class,'DeleteReader']);

});
