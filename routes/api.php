<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    ['prefix' => 'auth'],
    function () {
        Route::post('login', [UserController::class, 'login']);
        Route::post('recover', [UserController::class, 'recover']);
        Route::post('recover-post', [UserController::class, 'recoverPost']);
        Route::post('register', [UserController::class, 'register']);
        Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    }
);

/* Route::group(['prefix' => 'books', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BookController::class, 'index']);
    Route::post('add', [BookController::class, 'add']);
    Route::get('edit/{id}', [BookController::class, 'edit']);
    Route::post('update/{id}', [BookController::class, 'update']);
    Route::delete('delete/{id}', [BookController::class, 'delete']);
});
 */
