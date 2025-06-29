<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BagdeController;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//      Unauthenticated  routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/dashboard/login', [AuthController::class, 'webLogin']);





//---------------------Authenticated  routes---------------------------
Route::middleware('auth:sanctum')->group(function () {
    //--------------------------Auth--------------------------
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('auth/setup-profile', [AuthController::class, 'setupProfile']);
    Route::post('auth/edit-profile', [AuthController::class, 'editProfile']);

    //--------------------------Author------------------------
    Route::get('/getAuthors', [AuthorController::class, 'index']);
    Route::get('/getAuthors_D', [AuthorController::class, 'getAuthors_D']);
    Route::post('/AddAuthor_D', [AuthorController::class, 'AddAuthor']);
    Route::post('/editAuthor/{id}', [AuthorController::class, 'editAuthor']);
    Route::delete('/deleteAuthor_D/{authorId}', [AuthorController::class, 'deleteAuthor']);

    //---------------------------Book--------------------------
    Route::get('book/getBookFile/{BookId}', [BookController::class, 'getBookFile']);
    Route::get('book/getBooksComments/{BookId}', [BookController::class, 'getBooksComments']);
    Route::get('book/getNumbers', [BookController::class, 'getNumbers']);
    Route::get('book/getMostRatedBooks', [BookController::class, 'getMostRatedBooks']);
    Route::get('book/getAuthorBooks/{authorId}', [BookController::class, 'getAuthorBooks']);
    Route::get('book/getCategoryBooks/{categoryId}', [BookController::class, 'getCategoryBooks']);
    Route::get('book/getBooks', [BookController::class, 'getBooks']);
    Route::post('book/addBook', [BookController::class, 'addBook']);
    Route::delete('book/deleteBook/{bookId}', [BookController::class, 'deleteBook']);
    Route::get('book/getbookinfo/{bookId}', [BookController::class, 'getbookinfo']);

    //----------------------------Category----------------------------
    Route::get('/getCategories', [CategotyController::class, 'index']);
    Route::post('/addcategory', [CategotyController::class, 'addcategory']);
    Route::post('/editCategory/{id}', [CategotyController::class, 'editCategory']);

    //----------------------------Challenge--------------------------------
    Route::get('/getchallenges', [ChallengesController::class, 'index']);
    Route::get('/getchallenges_D', [ChallengesController::class, 'getchallenges']);
    Route::post('/editchallenge/{id}', [ChallengesController::class, 'editchallenge']);
    Route::post('/addChallenge', [ChallengesController::class, 'addChallenge']);
    Route::get('/getchallengeinfo/{id}', [ChallengesController::class, 'getchallengeinfo']);
    Route::delete('/deleteChallenge_D/{challengeId}', [ChallengesController::class, 'deleteChallenge']);

    //----------------------------Suggestion----------------------------
    Route::get('/getsuggestions', [SuggestionController::class, 'index']);
    Route::post('/UpdateSuggestion/{suggestionId}', [SuggestionController::class, 'UpdateSuggestion']);
    Route::get('/getSuggestionInfo/{suggestionId}', [SuggestionController::class, 'getSuggestionInfo']);
    Route::delete('/deleteSuggestion_D/{suggestionId}', [SuggestionController::class, 'deleteSuggestion']);

    //----------------------------Complaint-------------------------------
    Route::get('/getComplaints', [ComplaintController::class, 'getComplaints']);

    //----------------------------Admin--------------------------------------
    Route::get('/getAdminInfo/{adminId}', [UserController::class, 'getAdminInfo']);
    Route::post('/AddAdmin', [UserController::class, 'AddAdmin']);
    Route::delete('/deleteAdmin/{Adminid}', [UserController::class, 'deleteAdmin']);
    Route::get('/getAdmins', [UserController::class, 'getAdmins']);
    Route::get('/getAdminInfo/{adminId}', [UserController::class, 'getAdminInfo']);

    //-----------------------------Reader-------------------------------------
    Route::get('/getReaderInfo/{readerId}', [ReaderController::class, 'getReaderInfo']);
    Route::delete('/DeleteReader/{readerId}', [ReaderController::class, 'DeleteReader']);
    Route::get('/getReaders', [ReaderController::class, 'getReaders']);
    Route::get('/getReaderInfo/{readerId}', [ReaderController::class, 'getReaderInfo']);

    //-----------------------------Badge-------------------------------------
    Route::get('/getBadges_D', [BagdeController::class, 'getBadges']);
    Route::delete('/deletebadge_D/{badgeId}', [BagdeController::class, 'deletebadge']);
    Route::post('/addBadge_D', [BagdeController::class, 'addBadge']);
    Route::post('/editBadge/{id}', [BagdeController::class, 'editBadge']);
});
