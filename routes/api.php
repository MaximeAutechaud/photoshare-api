<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Services\S3Service;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $service = new S3Service();
    $s3url = \App\Models\Media::query()->select('s3_url')->where('user_id', 2)->get();
    foreach ($s3url as $url) {
        echo $url->s3_url . "\n";
    }
});
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/upload-files', [FileController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/destroy-file/{id}', [FileController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
Route::get('/fetch-gallery', [FileController::class, 'list'])->middleware('auth:sanctum');
