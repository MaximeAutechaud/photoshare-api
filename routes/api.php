<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/upload-files', [FileController::class, 'store'])->middleware('auth:sanctum');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
