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


// get all constant variables
Route::middleware('auth')->get('/get-const-var', 'BaseController@getConstVar');
Route::group(['middleware' => ['auth']], function() {
    Route::resources([
        'users' => 'UsersController',
    ]);
    Route::resources([
        'campaign' => 'CampaignController',
    ]);
});


// List users
Route::middleware('auth')->get('/user-list', 'UsersController@getUsersList');

// List Campaign Profile
Route::middleware('auth')->get('/campaign-list', 'CampaignController@getProfileList');
// upload leads email
Route::middleware('auth')->post('/upload-leads', 'CampaignController@uploadLeads');
// re-send campaign email
Route::middleware('auth')->get('/resend-campaign-email/{campId}', 'CampaignController@resendCampaignEmail');
// update campaign status
Route::middleware('auth')->get('/update-camp-status/{campId}/{status}', 'CampaignController@updateCampaignStatus');
// send campaign email
// Route::get('/campaign-email-send', 'CampaignController@sendCampaignEmail');

Route::group(['prefix' => 'campaign', 'middleware' => ['auth']], function() {
    // campaign unsend email list
    Route::get('/email-list/{campId}', 'CampaignController@getEmailList');
    // delete camp email
    Route::delete('/delete-email/{campId}/{email}', 'CampaignController@deleteEmail');
    Route::get('/export-leads-email/{campId}', 'CampaignController@exportLeadsEmail');
    // delete campaign attachment
    Route::delete('/delete-attachment/{id}', 'CampaignController@deleteAttachment');
    // update campaign
    Route::post('/update/{id}', 'CampaignController@update');
    Route::get('/export-invalid-email/{campId}', 'CampaignController@exportInvalidEmail');
});







