<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

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

// Route::group([
//     'prefix' => 'auth'
// ], function () {
//     Route::post('login', 'AuthController@login');
//     Route::post('signup', 'AuthController@signup');

//     Route::group([
//       'middleware' => 'auth:api'
//     ], function() {
//         Route::get('logout', 'AuthController@logout');
//         Route::get('user', 'AuthController@user');
//     });
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['prefix' => '/user', 'middleware' => 'auth:api'], function (){
//     Route::get('/profile', function (Request $request) {
//         return $request->user()->toArray();
//     })->middleware('scope:Profile');
//     Route::get('/email', function (Request $request) {
//         return $request->user()->email;
//     })->middleware('scope:Email');
// });

// Route::get('/user', function (Request $request) {
//     return $request->user()->toArray();
// })->middleware('auth:api');

Route::middleware('auth:api')->get('/user', 'UserController@getData');

Route::middleware('auth:api')->get('/authorizationCode', 'AuthorizationController@authorizationCode');

// Route::middleware('auth:api')->post('/authorizationCode', 'ApproveAuthorizationController@approve');

// Route::middleware('auth:api')->delete('/authorizationCode', 'DenyAuthorizationController@deny');


// Route::group(['middleware' => ['web', 'oauth']], function ($router) {
//     Route::get('/authorizationCode', [
//         'uses' => 'AuthorizationController@authorizationCode',
//         'as' => 'passport.authorizations.authorize',
//     ]);

//     Route::post('/authorizationCode', [
//         'uses' => 'ApproveAuthorizationController@approve',
//         'as' => 'passport.authorizations.approve',
//     ]);

//     Route::delete('/authorizationCode', [
//         'uses' => 'DenyAuthorizationController@deny',
//         'as' => 'passport.authorizations.deny',
//     ]);
// });
