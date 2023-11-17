<?php

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

Route::get('/', function () {
    return view('show_balance');
});
Route::get('/charge', function () {
    return view('charge');
});
Route::get('/use', function () {
    return view('use');
});
Route::get('/logs', function () {
    return view('logs');
});
Route::get('/all.css', function () {
    return response()->file('all.css', [
        'Content-Type' => 'text/css'
    ]);
});
