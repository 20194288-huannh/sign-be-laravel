<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RequestController;
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
Route::post('auth/register', [AuthController::class, 'register']);
Route::controller((AuthController::class))->prefix('auth')->group(function () {
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});

Route::controller(FileController::class)->prefix('files')->group(function () {
    Route::get('{id}', 'view');
    Route::get('{id}/download', 'download');
});

Route::controller(RequestController::class)->prefix('requests')->group(function () {
    Route::get('', 'show');
});

Route::middleware(['auth:user'])->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('email', [UserController::class, 'getByEmail']);
        Route::get('documents', [DocumentController::class, 'getDocumentByUser']);
        Route::post('key', [UserController::class, 'verifyPrivateKey']);
    });

    Route::controller(SignatureController::class)->prefix('signatures')->group(function () {
        Route::post('', 'store');
        Route::get('', 'index');
        Route::delete('{id}', 'destroy');
    });

    Route::controller(DocumentController::class)->prefix('documents')->group(function () {
        Route::get('statistics', 'getDocumentStatistic');
        Route::get('{id}', 'show');
        Route::post('{id}/sign', 'sign');
        Route::post('save', 'save');
        Route::get('', 'index');
        Route::post('', 'sign');
        Route::post('{id}/view-own', 'signOwn');
        Route::post('{id}/sign-own', 'saveSignOwn');
        Route::post('{id}/send-sign', 'sendSign');
        Route::post('{sha}/history', 'history');
        Route::get('{id}/actions', 'getActionsOfDocument');
    });

    Route::controller(ActionController::class)->prefix('actions')->group(function () {
        Route::get('', 'index');
    });

    Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
        Route::get('', 'index');
        Route::delete('{id}', 'destroy');
    });
});
