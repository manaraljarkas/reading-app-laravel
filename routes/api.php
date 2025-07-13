<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BagdeController;
use App\Http\Controllers\CategotyController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\SizeCategoryController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminPermissionController;
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
    Route::get('/author/getAuthors', [AuthorController::class, 'getAuthors']);
    Route::post('/author/update/{id}', [AuthorController::class, 'update']);
    Route::apiResource('authors', AuthorController::class)->except(['show', 'update']);



    //---------------------------Book--------------------------

    //---------------------------APIs using language middleware------------------------------
    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/books/most-rated', [BookController::class, 'getMostRatedBooks']);
        Route::get('/books/author-books/{authorId}', [BookController::class, 'getAuthorBooks']);
        Route::get('/books/category-books/{categoryId}', [BookController::class, 'getCategoryBooks']);

        Route::get('/books/GetBookChallenge/{Id}', [ChallengesController::class, 'GetBookChallenge']);
    });



    Route::get('book/getBookFile/{BookId}', [BookController::class, 'getBookFile']);
    Route::get('book/getBooksComments/{BookId}', [BookController::class, 'getBooksComments']);
    Route::get('book/getNumbers', [BookController::class, 'getNumbers']);
    Route::get('book/getCategoryBooks/{categoryId}', [BookController::class, 'getCategoryBooks']);
    Route::apiResource('books', BookController::class)->except(['update']);
    Route::get('book/AddBookToFavorite/{id}', [BookController::class, 'AddBookToFavorite']);
    Route::get('book/getBookComments/{id}', [BookController::class, 'getBookComments']);
    Route::get('book/AddBookToDoList/{id}', [BookController::class, 'AddBookToDoList']);
    Route::post('book/RateBook/{id}', [BookController::class, 'RateBook']);
    Route::post('book/AddCommentToTheBook/{id}', [BookController::class, 'AddCommentToTheBook']);



    // //----------------------------Category----------------------------
    Route::get('/category/getCategories', [CategotyController::class, 'getCategories']);
    Route::post('/category/update/{id}', [CategotyController::class, 'update']);
    Route::apiResource('categories', CategotyController::class)->except(['show', 'destroy']);



    // //----------------------------Challenge--------------------------------
    Route::get('/challenge/getchallenges', [ChallengesController::class, 'getchallenges']);
    Route::post('/challenge/update/{id}', [ChallengesController::class, 'update']);
    Route::apiResource('challenges', ChallengesController::class)->except(['update']);
    Route::get('/challenge/JoinToChallenge/{id}', [ChallengesController::class, 'JoinToChallenge']);



    //----------------------------Suggestion----------------------------
    Route::post('/suggestion/Update/{id}', [SuggestionController::class, 'update']);
    Route::apiResource('suggestions', SuggestionController::class)->except(['store']);



    //----------------------------Complaint-------------------------------
    Route::get('/complaint/getComplaints', [ComplaintController::class, 'getComplaints']);




    //----------------------------Admin--------------------------------------
    Route::apiResource('admins', UserController::class)->except(['update']);



    //-----------------------------Reader-------------------------------------
    Route::apiResource('readers', ReaderController::class)->except(['store', 'update']);




    //-----------------------------Badge-------------------------------------
    Route::post('/badge/update/{id}', [BagdeController::class, 'update']);
    Route::apiResource('badges', BagdeController::class);



    //------------------------------Permissions------------------------------------
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin-permissions/{admin}', [AdminPermissionController::class, 'show']);
        Route::post('/admin-permissions/{admin}', [AdminPermissionController::class, 'update']);
    });


    
});
