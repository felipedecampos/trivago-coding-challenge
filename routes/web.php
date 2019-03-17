<?php

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

use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    Log::channel('application')->info(
        'The customer enters in the home application.',
        auth()->check() ? [auth()->user()->getAuthIdentifierName() => auth()->user()->getAuthIdentifier()] : []
    );
    return view('welcome');
});

Auth::routes();

Route::get('/home', function (){ return redirect('orders'); });

Route::middleware('auth')->group(function () {
    Route::resources(['orders' => 'OrderController']);
});