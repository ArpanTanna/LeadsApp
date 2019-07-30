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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', 'Auth\LoginController@showLoginForm');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
//Route::get('redtoauth', 'HomeController@redtoauth');

Route::get('emailopen/{eikey}/{leadkey}', 'HomeController@emailopen');

Route::group(['prefix' => 'client'], function()
{
    //Route::resources(['photos' => 'PhotoController']);
    Route::get('dashboard', 'Client\DashboardController@index');
    Route::post('lead/apiaccess/{method}', 'Client\LeadController@apiaccess');
    Route::post('funnel/apiaccess/{method}', 'Client\FunnelController@apiaccess');
    Route::post('emailmanager/apiaccess/{method}', 'Client\EmailmanagerController@apiaccess');
    Route::resources([
        'lead' => 'Client\LeadController',
        'funnel' => 'Client\FunnelController',
        'emailmanager' => 'Client\EmailmanagerController'
    ]);
});
