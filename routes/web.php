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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/direction', 'HomeController@getDistance')->name('distance');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'files', 'as' => 'files.','middleware' => ['web', 'auth']], function() {

    Route::get('/index', 'FileuploadController@index')->name('index');
    Route::get('/upload', 'FileuploadController@create')->name('upload');
    Route::post('/store', 'FileuploadController@store')->name('store');
    Route::post('/destroy', 'FileuploadController@destroy')->name('destroy');
    Route::post('/download', 'FileuploadController@download')->name('download');
    Route::get('/send', 'FileuploadController@notification')->name('send');
});

Route::group(['prefix' => 'users', 'as' => 'users.','middleware' => ['web', 'auth']], function() {

    Route::get('/user/{user}', 'UserController@index')->name('user');
    Route::put('/user/{user}', 'UserController@update')->name('update');
    Route::post('/user', 'UserController@create')->name('register');

});
