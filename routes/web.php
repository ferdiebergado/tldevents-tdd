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

Route::group(['middleware' => ['auth', 'verified', 'active']], function () {

    Route::post('/events', 'EventController@store');
    Route::put('/events/{event}', 'EventController@update');
    Route::delete('/events/{event}', 'EventController@destroy');
    Route::delete('/events/{event}/force', 'EventController@forceDestroy');
    Route::post('/events/{id}/restore', 'EventController@restore');
    Route::get('/events/{event}', 'EventController@show');
    Route::get('/events', 'EventController@index');

    Route::post('/participants', 'ParticipantController@store');
    Route::put('/participants/{participant}', 'ParticipantController@update');
    Route::delete('/participants/{participant}', 'ParticipantController@destroy');
    Route::delete('/participants/{participant}/force', 'ParticipantController@forceDestroy');
    Route::post('/participants/{id}/restore', 'ParticipantController@restore');
});
