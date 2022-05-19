<?php

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/passport', function () {
    return view('passport');
})->name('passport');

Route::get('/test', 'HomeController@test')->name('testWelcome');

Route::get('/home', 'HomeController@index')->name('home');

// Route::middleware('auth')->get('/login', function () {
//     return view('login');
// });

// Route::post('/authorizationCode','ApproveAuthorizationController@approve')->name('passport.authorizations.approve');

//auth:api
// Route::get('/authorizationCode', [
//     'uses' => 'AuthorizationController@authorizationCode',
//     'as' => 'passport.authorizations.authorize',
// ]);
Route::group(['middleware' => ['web', 'oauth']], function ($router) {
    Route::get('/authorizationCode', [
        'uses' => 'AuthorizationController@authorizationCode',
        'as' => 'passport.authorizations.authorize',
    ]);

    Route::post('/authorizationCode', [
        'uses' => 'ApproveAuthorizationController@approve',
        'as' => 'passport.authorizations.approve',
    ]);

    Route::delete('/authorizationCode', [
        'uses' => 'DenyAuthorizationController@deny',
        'as' => 'passport.authorizations.deny',
    ]);
});

// Route::get('/authorizationCode', 'AuthorizationController@authorizationCode')->name('authorizationCode');

// Route::post('/authorizationCode', 'ApproveAuthorizationController@approve')->name('approve');

// Route::delete('/authorizationCode', 'DenyAuthorizationController@deny')->name('deny');

Route::get('/getCSRFToken', 'HomeController@getCSRFToken');

Route::get('/permission', 'PermissionController@getDefaultData');

Route::post('/savePermission', 'PermissionController@savePermission')->name('savePermission');
