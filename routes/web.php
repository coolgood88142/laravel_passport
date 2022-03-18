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

Route::get('/home', 'HomeController@index')->name('home');

// Route::middleware('auth')->get('/login', function () {
//     return view('login');
// });

Route::group(['middleware' => ['web', 'auth']], function ($router) {
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

Route::post('/accessToken', [
    'uses' => 'AccessTokenController@issueToken',
    'as' => 'passport.token',
    'middleware' => 'throttle',
]);

Route::group(['middleware' => ['web', 'auth']], function ($router) {
    Route::get('/accessToken', [
        'uses' => 'AuthorizedAccessTokenController@forUser',
        'as' => 'passport.tokens.index',
    ]);

    Route::delete('/accessToken/{token_id}', [
        'uses' => 'AuthorizedAccessTokenController@destroy',
        'as' => 'passport.tokens.destroy',
    ]);
});

// Route::get('/authorizationCode', 'AuthorizationController@authorizationCode')->name('authorizationCode');

// Route::post('/authorizationCode', 'ApproveAuthorizationController@approve')->name('approve');

// Route::delete('/authorizationCode', 'DenyAuthorizationController@deny')->name('deny');

Route::get('/accessToken', 'AccessTokenController@issueToken')->name('accessToken');
