<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/recupero-password/{token}/', [HomeController::class, 'recoverPassword'])->name('password.reset');

Route::get('{any}', [HomeController::class, 'app'])->where('any', '.*');
