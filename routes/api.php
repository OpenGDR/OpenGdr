<?php

use App\Http\Controllers\API\LogController;
use App\Http\Controllers\API\PermissionController;
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

/**
 * Parte pubblica
 */
Route::group(
    ['prefix' => 'auth'],
    function () {
        Route::post('login', [UserController::class, 'login']);
        Route::post('recover', [UserController::class, 'recover']);
        Route::post('recover-post', [UserController::class, 'recoverPost']);
        Route::post('register', [UserController::class, 'register']);
        Route::post('logout', [UserController::class, 'logout']);

        Route::group(
            ['middleware' => 'auth:sanctum'],
            function () {
                Route::post('email-verify', [UserController::class, 'emailVerify']);
                Route::post('email-resend', [UserController::class, 'emailResend']);
                Route::group(
                    ['prefix' => 'permission'],
                    function () {
                        Route::get('check/{permission}/{model}/{id?}', [PermissionController::class, 'check']);
                    }
                );
            }
        );
    }
);

/**
 * Gestione utente
 */
Route::group(
    ['prefix' => 'user', 'middleware' => 'auth:sanctum'],
    function () {
        Route::get('data/{id?}', [UserController::class, 'getUserData']);
        Route::post('general', [UserController::class, 'updateUserDataGeneral']);
        Route::post('new-password', [UserController::class, 'updateUserPassword']);
    }
);


/**
 * Amministrazione
 */
Route::group(
    ['prefix' => 'admin', 'middleware' => 'auth:sanctum'],
    function () {
        Route::group(
            ['prefix' => 'user'],
            function () {
                Route::get('get-list', [UserController::class, 'getAdminListUsers']);
            }
        );
        Route::group(
            ['prefix' => 'log'],
            function () {
                Route::get('get-list', [LogController::class, 'getAdminList']);
            }
        );
    }
);
