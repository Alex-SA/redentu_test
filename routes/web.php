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
        // Task 1
Route::get('/image', 'ImageController@index');
Route::get('/list', 'ImageController@list');
Route::post('/upload', 'ImageController@upload');

        // Task 2
Route::get('/watermark', 'UploadWaterMarkController@index')->name('upload_wm');
Route::post('/watermark', 'UploadWaterMarkController@store');

        // Task 3
Route::get('/text_watermark', 'UploadTextWaterMarkController@index')->name('upload_text_wm');
Route::post('/text_watermark', 'UploadTextWaterMarkController@store');

        // Task 4, 5
Route::get('/image_with_watermark', 'UploadImageWithWaterMarkController@index')->name('image_with_wm');
Route::post('/image_with_watermark', 'UploadImageWithWaterMarkController@store');

    // Task 6
Route::get('/crop_image', 'CropImageController@index')->name('image_crop');
Route::post('/crop_image', 'CropImageController@store');


        // All-in-One (draft)
Route::get('/load', 'LoadImageController@index')->name('load');
Route::post('/load/watermark', 'LoadImageController@watermark')->name('watermark');
Route::post('/load/text', 'LoadImageController@watermarkText')->name('watermark.text');
Route::post('/load/image', 'LoadImageController@saveImage')->name('image');
Route::post('/load/crop', 'LoadImageController@cropImage')->name('crop');
