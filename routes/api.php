<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
