<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('auth/login', [AuthController::class, 'login']);
// Route::middleware(['auth:user'])->group(function () {
Route::controller((AuthController::class))->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});

Route::controller(UserController::class)->prefix('users')->group(function () {
    Route::get('{id}/documents', [DocumentController::class, 'getDocumentByUser']);
});

Route::controller(SignatureController::class)->prefix('signatures')->group(function () {
    Route::post('', 'store');
});

Route::controller(DocumentController::class)->prefix('documents')->group(function () {
    Route::post('{id}/sign', 'sign');
    Route::post('save', 'save');
    Route::get('', 'index');
    Route::post('', 'sign');
    Route::post('{id}/sign-own', 'signOwn');
});

Route::controller(FileController::class)->prefix('files')->group(function () {
    Route::get('{id}', 'view');
    Route::get('{id}/download', 'download');
});
