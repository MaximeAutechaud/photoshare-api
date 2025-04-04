<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
