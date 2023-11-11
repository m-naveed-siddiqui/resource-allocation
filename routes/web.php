<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/resources', [HomeController::class, 'resources'])->name('resources');
Route::post('/allocation', [HomeController::class, 'allocation'])->name('allocation');
Route::post('/assign', [HomeController::class, 'assign'])->name('assign');

Route::get('/detail', [HomeController::class, 'detail'])->name('detail');
Route::post('/result', [HomeController::class, 'result'])->name('result');
