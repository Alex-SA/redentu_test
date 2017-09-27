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

Route::get('/load', 'LoadImageController@index')->name('load');
Route::post('/load/watermark', 'LoadImageController@watermark')->name('watermark');
Route::post('/load/text', 'LoadImageController@watermarkText')->name('watermark.text');
Route::post('/load/image', 'LoadImageController@saveImage')->name('image');
Route::post('/load/crop', 'LoadImageController@cropImage')->name('crop');
