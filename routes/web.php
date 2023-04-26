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

Auth::routes();
Route::get('/', 'DashboardController@index');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/logout','Auth\LoginController@logout');
Route::middleware('auth')->get('/users','UsersController@index')->name('users');
Route::middleware('auth')->get('/campaign','CampaignController@index')->name('campaign');

// email template builder template routing
Route::group(['middleware' => ['auth'],'prefix' => 'email-template'], function()
{ 
	Route::get('/template-builder', 'EmailTemplateController@getTemplateBuilder');
    Route::get('/load-page', 'EmailTemplateController@loadPage');
    Route::get('/blank-page', 'EmailTemplateController@blankPage');
    Route::get('/email-lang', 'EmailTemplateController@emailLang');
    Route::post('/update-block-info', 'EmailTemplateController@updateBlockInfo');
    Route::get('/load-templates', 'EmailTemplateController@loadTemplates');
    Route::get('/get-files', 'EmailTemplateController@getFiles');
    Route::post('/save', 'EmailTemplateController@save');
    Route::post('/get-template-blocks', 'EmailTemplateController@getTemplateBlocks');
    Route::post('/upload', 'EmailTemplateController@upload');
    Route::post('/delete', 'EmailTemplateController@delete');
    Route::post('/import', 'EmailTemplateController@import');
    Route::post('/upload-template-image', 'EmailTemplateController@uploadTemplateImage');
    Route::post('/export', 'EmailTemplateController@export');
    Route::post('/email-send', 'EmailTemplateController@emailSend');
    
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
