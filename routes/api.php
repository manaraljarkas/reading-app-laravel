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
use App\Http\Controllers\BookChallengeController;
use App\Http\Controllers\SizeCategoryController;
use App\Http\Controllers\ReaderBookController;
use Illuminate\Support\Facades\Route;



// ==================== Unauthenticated Routes ====================
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/dashboard/login', [AuthController::class, 'webLogin']);


// ==================== Authenticated Routes ====================
Route::middleware('auth:sanctum')->group(function () {

    // ---------- Authentication ----------
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('auth/setup-profile', [AuthController::class, 'setupProfile']);
    Route::post('auth/edit-profile', [AuthController::class, 'editProfile']);

    // ---------- Reader ----------
    Route::get('reader/getAllProfiles', [ReaderController::class, 'getAllProfiles']);
    Route::get('/reader/showProfile/{id}', [ReaderController::class, 'showProfile']);
    Route::get('/reader/showProfile', [ReaderController::class, 'showProfile']);

    // ---------- Complaint ----------
    Route::post('complaint/store', [ComplaintController::class, 'createComplaint']);


    // ==================== Author ====================
    Route::controller(AuthorController::class)->group(function () {
        Route::get('authors', 'index')->middleware(['auth:sanctum', 'permission:read author']);
        Route::get('authors/{id}', 'show')->middleware(['auth:sanctum', 'permission:read author']);
        Route::post('authors', 'store')->middleware(['auth:sanctum', 'permission:create author']);
        Route::post('/author/update/{id}', 'update')->middleware(['auth:sanctum', 'permission:update author']);
        Route::delete('authors/{id}', 'destroy')->middleware(['auth:sanctum', 'permission:delete author']);
    });


    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/author/getAuthors', [AuthorController::class, 'getAuthors']);
        Route::get('search/authors', [AuthorController::class, 'searchAuthors']);
    });

    // ==================== Book ====================
    Route::get('book/getBookFile/{BookId}', [BookController::class, 'getBookFile']);
    Route::get('book/getNumbers', [BookController::class, 'getNumbers']);
    Route::controller(BookController::class)->group(function () {
        Route::get('books', 'index')->middleware(['auth:sanctum', 'permission:read book']);
        Route::get('books/{id}', 'show')->middleware(['auth:sanctum', 'permission:read book']);
        Route::post('books', 'store')->middleware(['auth:sanctum', 'permission:create book']);
        Route::post('/book/update/{id}', 'update')->middleware(['auth:sanctum', 'permission:update book']);
        Route::delete('books/{id}', 'destroy')->middleware(['auth:sanctum', 'permission:delete book']);
    });

    // Book Challenges
    Route::controller(BookChallengeController::class)->group(function () {
        Route::post('/bookchallenge/create', 'store')->middleware(['auth:sanctum', 'permission:create book']);
        Route::post('/bookchallenge/update/{id}', 'update')->middleware(['auth:sanctum', 'permission:update book']);
    });

    // ReaderBook actions
    Route::get('book/AddBookToFavorite/{id}', [ReaderBookController::class, 'AddBookToFavorite']);
    Route::get('book/getBookComments/{id}', [BookController::class, 'getBookComments']);
    Route::get('book/AddBookToDoList/{id}', [ReaderBookController::class, 'AddBookToDoList']);
    Route::post('book/RateBook/{id}', [ReaderBookController::class, 'RateBook']);
    Route::post('book/AddCommentToTheBook/{id}', [BookController::class, 'AddCommentToTheBook']);
    Route::post('book/update-reading-progress/{id}', [ReaderBookController::class, 'updateReadingProgress']);
    Route::post('book/remove-from-favorites/{id}', [ReaderBookController::class, 'removeFromFavorites']);

    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/books/most-rated', [ReaderBookController::class, 'getMostRatedBooks']);
        Route::get('/books/author-books/{authorId}', [ReaderBookController::class, 'getAuthorBooks']);
        Route::get('/books/category-books/{categoryId}', [ReaderBookController::class, 'getCategoryBooks']);
        Route::get('/books/favorites', [ReaderBookController::class, 'getFavoriteBooks']);
        Route::get('/books/toread', [ReaderBookController::class, 'getToReadBooks']);
        Route::get('/books/inread', [ReaderBookController::class, 'getInReadBooks']);
        Route::get('/books/completed', [ReaderBookController::class, 'getCompletedBooks']);
        Route::get('/search/books', [BookController::class, 'searchBooks']);
        Route::get('/books/GetBookChallenge/{Id}', [ChallengesController::class, 'GetBookChallenge']);
    });

    // ==================== Category ====================
    // Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    // Route::apiResource('categories', CategoryController::class);

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->middleware(['auth:sanctum', 'permission:read category']);
        Route::get('categories/{id}', 'show')->middleware(['auth:sanctum', 'permission:read category']);
        Route::post('categories', 'store')->middleware(['auth:sanctum', 'permission:create category']);
        Route::post('/category/update/{id}', 'update')->middleware(['auth:sanctum', 'permission:update category']);
        Route::delete('categories/{id}', 'destroy')->middleware(['auth:sanctum', 'permission:delete category']);
    });
    Route::get('/category/getCategories', [CategoryController::class, 'getCategories']);
    Route::post('/categories/follow/{category}', [CategoryController::class, 'followCategory']);
    Route::delete('/categories/unfollow/{category}', [CategoryController::class, 'unfollowCategory']);

    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/category/getCategories', [CategoryController::class, 'getCategories']);
        Route::get('search/category', [CategoryController::class, 'searchCategories']);
    });

    // ==================== Challenge ====================
    Route::post('/challenge/update/{id}', [ChallengesController::class, 'update']);
    Route::apiResource('challenges', ChallengesController::class)->except(['update']);
    Route::get('/challenge/JoinToBookChallenge/{id}', [ChallengesController::class, 'JoinToBookChallenge']);
    Route::post('/challenge/JoinToChallenge/{id}', [ChallengesController::class, 'JoinToChallenge']);
    Route::get('/challenge/getAllChallenges', [ChallengesController::class, 'getAllChallenges']);

    Route::prefix('mobile')->middleware('set.lang')->group(function () {
        Route::get('/challenge/getchallenges', [ChallengesController::class, 'getchallenges']);
    });

    // ==================== Suggestion ====================
    Route::post('/suggestion/Update/{id}', [SuggestionController::class, 'update']);
    Route::post('/suggestion/store', [SuggestionController::class, 'store']);

    // ==================== Country ====================
    Route::controller(CountryController::class)->group(function () {
        Route::get('countries', 'index')->middleware(['auth:sanctum', 'permission:read country']);
        Route::get('countries/{country_id}', 'show')->middleware(['auth:sanctum', 'permission:read country']);
        Route::post('countries', 'store')->middleware(['auth:sanctum', 'permission:create country']);
        Route::post('/country/update/{country_id}', 'update')->middleware(['auth:sanctum', 'permission:update country']);
        Route::delete('countries/{country_id}', 'destroy')->middleware(['auth:sanctum', 'permission:delete country']);
    });
    Route::get('/country/get-trips', [CountryController::class, 'getTrips']);

    // ==================== Size Category ====================
    Route::apiResource('size-categories', SizeCategoryController::class);
    Route::post('/size-category/update/{size_category_id}', [SizeCategoryController::class, 'update']);

    // ==================== Badge ====================
    Route::controller(BagdeController::class)->group(function () {
        Route::get('badges', 'index')->middleware(['auth:sanctum', 'permission:read badge']);
        Route::get('badges/{id}', 'show')->middleware(['auth:sanctum', 'permission:read badge']);
        Route::post('badges', 'store')->middleware(['auth:sanctum', 'permission:create badge']);
        Route::post('/badge/update/{id}', 'update')->middleware(['auth:sanctum', 'permission:update badge']);
        Route::delete('badges/{id}', 'destroy')->middleware(['auth:sanctum', 'permission:delete badge']);
    });

    // ==================== Admin (Super Admin Only) ====================
    Route::middleware('role:super_admin')->group(function () {
        Route::apiResource('admins', UserController::class);
        Route::post('/admin/update/{id}', [UserController::class, 'update']);

        Route::apiResource('readers', ReaderController::class)->except(['store', 'update']);

        Route::get('/admin-permissions/{admin}', [AdminPermissionController::class, 'show']);
        Route::post('/admin-permissions/{admin}', [AdminPermissionController::class, 'update']);

        Route::get('/complaint/getComplaints', [ComplaintController::class, 'getComplaints']);
        Route::apiResource('suggestions', SuggestionController::class);
    });


    Route::post('/suggestion/store', [SuggestionController::class, 'store']);

    Route::get('/admin/getAdmin', [UserController::class, 'getAdmin']);
    Route::get('/admin-permissions', [AdminPermissionController::class, 'showCurrent']);
});
