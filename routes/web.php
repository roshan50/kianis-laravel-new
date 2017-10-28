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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/members', 'MemberController@index');
Route::get('/members/create', 'MemberController@create');
Route::post('/members', 'MemberController@store');
Route::get('/members/{member}', 'MemberController@show');
Route::get('/members/{member}/edit', 'MemberController@edit');
Route::put('/members/{member}', 'MemberController@update');
Route::delete('/members/{member}', 'MemberController@delete');
