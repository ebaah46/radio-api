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
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', 'UserApiController@login')->name('user.login');
Route::post('register', 'UserApiController@register')->name('user.register');
//    Group of routes to handle messages
Route::group(['middleware'=>'auth:api'],function(){
    Route::prefix('/messages')->group(function(){
    //    Route to handle message creation
        Route::post('/create','MessageApiController@store')->name('messages.create');
    //    Route to handle messages retrieval
        Route::get('/index','MessageApiController@index')->name('messages.index');
    //    Route to download particular message
        Route::get('/download/{id}','MessageApiController@download')->name('messages.download');
        Route::get('/play/{id}','MessageApiController@show')->name('messages.play');
        Route::get('/info','MessageApiController@checkInfo')->name('messages.info');
    });
});
