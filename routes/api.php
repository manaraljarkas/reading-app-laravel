<?php

use App\Http\Controllers\AuthController;
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






});
