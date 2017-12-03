<?php

use Illuminate\Http\Request;

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

Route::post('/user/register', 'Auth\RegisterController@apiRegister');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/data/{data}' , 'DataController@show')->middleware(['auth:api' ]);

Route::resource('data', 'DataController', ['except' => [
        'create', 'edit', 'update', 'show'
]])->middleware(['auth:api' ]);