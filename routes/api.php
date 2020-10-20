<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//Login
Route::post('/auth/user','App\Http\Controllers\Api\UserController@authenticate');
Route::post('/user',"App\Http\Controllers\Api\UserController@store");

//Protected Routes
Route::group([

    'middleware' => 'auth:api'

], function () {
    Route::get('/auth/user','App\Http\Controllers\Api\UserController@read');
    Route::get('/auth/user/logout','App\Http\Controllers\Api\UserController@logout');
    Route::get('/posts/{pagination?}', 'App\Http\Controllers\Api\PostController@index');



    //post Related
    Route::delete('post/{id}','App\Http\Controllers\Api\PostController@destroy');

    Route::post('/post','App\Http\Controllers\Api\PostController@store');
    Route::get('/post/like/{post_id}','App\Http\Controllers\Api\PostController@storePostLikeInfo');

//Comment Routes

    Route::post('/comment', 'App\Http\Controllers\Api\CommentController@store');
    Route::delete('/comment/{id}', 'App\Http\Controllers\Api\CommentController@destroy');
    Route::get('/comments/{post_id?}/{pagination?}', 'App\Http\Controllers\Api\CommentController@index');

//Sub Comments Routes
    Route::get('/subComments/{parent_id}', 'App\Http\Controllers\Api\CommentController@loadMoreComments');
});
//Post Routes
//Route::get('/posts/{pagination?}/{userId?}', 'App\Http\Controllers\Api\PostController@index');





