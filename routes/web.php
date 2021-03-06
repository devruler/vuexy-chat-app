<?php

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

Route::get('/pages/login', 'ApplicationController')->name('login');
Route::get('/pages/register', 'ApplicationController')->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/{any}', 'ApplicationController')->where('any', '.*');
});

