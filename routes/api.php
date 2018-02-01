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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {
    Route::post('/index/user','IndexController@user');   //保存用户信息
    Route::post('/index/user_info','IndexController@user_info');   //获取用户信息
    Route::post('/index/simple','IndexController@simple');   //获取精简版
    Route::post('/index/interest','IndexController@interest');   //获取趣味版

    Route::post('/index/openid','IndexController@openid');   //获取openid


    Route::post('/index/my_mingpan','IndexController@my_mingpan');   //我的命盘

    Route::post('/index/index','IndexController@index');



    Route::get('/index/test','IndexController@test');
});
