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
Route::get('email/verify/{id}/{hash}', [HomeController::class, 'app'])->name('verification.verify');
Route::post('email/resend', [HomeController::class, 'app'])->name('verification.resend');

Route::post('utente/profilo/{id}', [HomeController::class, 'app'])->name('user.profile');

Route::post('/admin/race/update/{slug?}', [HomeController::class, 'app'])->name('admin.race.update');

Route::get('{any}', [HomeController::class, 'app'])->where('any', '.*');
