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

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::get('/data/import' , 'DataController@import')->name('data.import');
    Route::get('/data/{data}' , 'DataController@show')->name('data.show');
    Route::delete('/data/{data}' , 'DataController@destroy')->name('data.destroy');
    Route::post('/data/ajax', 'DataController@ajaxtest')->name('data.ajax');
    Route::post('/data/ajax/import', 'DataController@ajaxImport')->name('data.ajaximport');

    Route::resource('file', 'FileController');

    Route::resource('data', 'DataController', ['except' => [
        'create', 'edit', 'update', 'show', 'destroy'
    ]]);


    

});

Route::get('/home', 'HomeController@index')->name('home');
