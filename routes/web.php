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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::group(['middleware' => ['auth', 'verified']], function () {

    Route::post('/events', 'EventController@store');
    Route::put('/events/{event}', 'EventController@update');
    Route::delete('/events/{event}', 'EventController@destroy');
    Route::get('/events/{event}', 'EventController@show');
    Route::get('/events', 'EventController@index');
});
