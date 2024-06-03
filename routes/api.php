<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('presigned-url', [UploadController::class, 'getPresignedUrl']);
Route::get('post-presigned-url', [UploadController::class, 'createPresignedUrl']);
Route::get('get-upload-id', [UploadController::class, 'getUploadId']);
Route::get('get-multipart-presigned-url', [UploadController::class, 'getPresignedUploadPartUrl']);
Route::post('complete-upload-parts', [UploadController::class, 'completeUploadPart']);
