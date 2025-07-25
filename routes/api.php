<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BagdeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminPermissionController;
use App\Http\Controllers\SizeCategoryController;
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
    Route::post('/author/update/{id}', [AuthorController::class, 'update']);
    Route::apiResource('authors', AuthorController::class);


    //---------------------------APIs using language middleware------------------------------
    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/author/getAuthors', [AuthorController::class, 'getAuthors']);
    });



    //---------------------------Book--------------------------

    //---------------------------APIs using language middleware------------------------------
    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/books/most-rated', [BookController::class, 'getMostRatedBooks']);
        Route::get('/books/author-books/{authorId}', [BookController::class, 'getAuthorBooks']);
        Route::get('/books/category-books/{categoryId}', [BookController::class, 'getCategoryBooks']);
        Route::get('/books/favorites', [BookController::class, 'getFavoriteBooks']);
        Route::get('/books/toread', [BookController::class, 'getToReadBooks']);
        Route::get('/books/inread', [BookController::class, 'getInReadBooks']);
        Route::get('/books/completed', [BookController::class, 'getCompletedBooks']);

        Route::get('/books/GetBookChallenge/{Id}', [ChallengesController::class, 'GetBookChallenge']);
    });

    Route::get('book/getBookFile/{BookId}', [BookController::class, 'getBookFile']);
    Route::get('book/getNumbers', [BookController::class, 'getNumbers']);
    Route::get('book/getCategoryBooks/{categoryId}', [BookController::class, 'getCategoryBooks']);
    Route::apiResource('books', BookController::class);
    Route::post('/book/update/{id}', [BookController::class, 'update']);
    Route::get('book/AddBookToFavorite/{id}', [BookController::class, 'AddBookToFavorite']);
    Route::get('book/getBookComments/{id}', [BookController::class, 'getBookComments']);
    Route::get('book/AddBookToDoList/{id}', [BookController::class, 'AddBookToDoList']);
    Route::post('book/RateBook/{id}', [BookController::class, 'RateBook']);
    Route::post('book/AddCommentToTheBook/{id}', [BookController::class, 'AddCommentToTheBook']);



    // //----------------------------Category----------------------------
    //---------------------------APIs using language middleware------------------------------
    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/category/getCategories', [CategoryController::class, 'getCategories']);
    });

    Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    Route::apiResource('categories', CategoryController::class);
    Route::get('/category/getCategories', [CategoryController::class, 'getCategories']);
    Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    Route::apiResource('categories', CategoryController::class)->except(['show', 'destroy']);
    Route::post('/categories/{category}/follow', [CategoryController::class, 'followCategory']);
    Route::delete('/categories/{category}/unfollow', [CategoryController::class, 'unfollowCategory']);



    // //----------------------------Challenge--------------------------------
    Route::post('/challenge/update/{id}', [ChallengesController::class, 'update']);
    Route::apiResource('challenges', ChallengesController::class)->except(['update']);
    Route::get('/challenge/JoinToBookChallenge/{id}', [ChallengesController::class, 'JoinToBookChallenge']);

    //---------------------------APIs using language middleware------------------------------
    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/challenge/getchallenges', [ChallengesController::class, 'getchallenges']);
    });


    //----------------------------Suggestion----------------------------
    Route::post('/suggestion/Update/{id}', [SuggestionController::class, 'update']);


    //------------------------------------Country-------------------------------------
    Route::apiResource('countries', CountryController::class);
    Route::post('/country/update/{country_id}', [CountryController::class, 'update']);
    Route::get('/country/get-trips', [CountryController::class, 'getTrips']);


    //------------------------------------Size Category-------------------------------------
    Route::apiResource('size-categories', SizeCategoryController::class);
    Route::post('/size-category/update/{size_category_id}', [SizeCategoryController::class, 'update']);



    Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
        //----------------------------Admin--------------------------------------
        Route::apiResource('admins', UserController::class);
        Route::post('/admin/update/{id}', [UserController::class, 'update']);
        //-----------------------------Reader-------------------------------------
        Route::apiResource('readers', ReaderController::class)->except(['store', 'update']);
        //------------------------------Permissions------------------------------------
        Route::get('/admin-permissions/{admin}', [AdminPermissionController::class, 'show']);
        Route::post('/admin-permissions/{admin}', [AdminPermissionController::class, 'update']);
        //----------------------------Complaints & Suggestions-------------------------------
        Route::get('/complaint/getComplaints', [ComplaintController::class, 'getComplaints']);
        Route::apiResource('suggestions', SuggestionController::class)->except(['store']);
    });
    Route::get('/admin/getAdmin', [UserController::class, 'getAdmin']);
    Route::get('/admin-permissions', [AdminPermissionController::class, 'showCurrent']);

    //-----------------------------Badge-------------------------------------
    Route::post('/badge/update/{id}', [BagdeController::class, 'update']);
    Route::apiResource('badges', BagdeController::class);
});
