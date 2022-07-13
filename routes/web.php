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
Route::group(['middleware' => ['web', 'oauth', 'swfix']], function ($router) {
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

Route::get('/permission', 'PermissionController@getDefaultData')->name('permission');

Route::post('/savePermission', 'PermissionController@savePermission')->name('savePermission');

Route::get('/getLoginSession', 'UserController@getLoginSesstion');

Route::get('/mainPermission', 'PermissionController@showUserPermissionBlade');

Route::get('/editTrial', 'TrialController@editTrialData')->name('editTrial');

Route::get('/queryTrial', 'TrialController@queryTrialData')->name('queryTrial');

Route::post('/saveTrial', 'TrialController@saveTrialData')->name('saveTrial');

Route::post('/getBearerToken', 'AuthController@getBearerToken')->name('getBearerToken');

Route::get('/queryPermissionAmount', 'PermissionAmountController@queryPermissionAmountData')->name('queryPermissionAmount');

Route::get('/getUserPermission/{companyId}/{productId}/{startDatetime}/{endDatetime}', 'PermissionController@getUserPermission')->name('getUserPermission');

Route::get('/getUserPermissionDeatils', 'PermissionController@getUserPermissionDeatils')->name('getUserPermissionDeatils');

Route::get('editCompany', 'CompanyController@editCompany')->name('editCompany');

Route::get('queryCompany', 'CompanyController@queryCompany')->name('queryCompany');

Route::get('sendCompanyData', 'CompanyController@sendCompanyData')->name('sendCompanyData');

Route::get('/getAuthorizationPage', 'AuthorizationController@getAuthorizationPage')->name('authorizationPage');

Route::get('/agreeAuthorization', 'AuthorizationController@agreeAuthorization')->name('agreeAuthorization');

Route::get('/sendEmail', 'MailController@ship')->name('sendEmail');

Route::get('/googlesheet', 'GoogleSheetsController@sheetOperation')->name('googlesheet');

Route::get('/createGoogleSheet', 'GoogleSheetsController@createSpreadsheets')->name('createGoogleSheet');

Route::get('/createCompanyData', 'GoogleSheetsController@createCompanyData')->name('createCompanyData');

Route::get('/exportExcel', 'MailController@sendEmailWithAttach');
