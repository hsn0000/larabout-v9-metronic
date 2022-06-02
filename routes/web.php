<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Contracts\LogoutResponse;

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

Route::get('logout', function(StatefulGuard $guard) {
    $guard->logout();
    session()->invalidate();
    session()->regenerateToken();
    return app(LogoutResponse::class);
});

Route::namespace('\App\Modules\Home\Controllers')->group(function () {
    Route::prefix('/')->group(function() {
        Route::get('/', 'HomeController@index')->name('home')->middleware(['auth:sanctum', 'verified']);
        Route::post('/set-timezone', 'HomeController@timezone')->middleware('ajax');
    });
});

Route::namespace('\App\Modules\User\Controllers')->group(function () {
    // user  
    Route::prefix('/user')->name('user')->group(function() {
        Route::get('/', 'UserController@index')->middleware(['auth:sanctum', 'verified']);
        Route::get('/json', 'UserController@json')->name('2')->middleware(['auth:sanctum', 'verified']);
        Route::post('/data-table', ['as' => '.dt', 'uses' => 'UserController@data_table'])->middleware('ajax');
        Route::get('/add', 'UserController@add')->name('.add')->middleware(['auth:sanctum', 'verified']);
        Route::post('/save', 'UserController@save')->name('.save')->middleware(['auth:sanctum', 'verified']);
        Route::get('/edit/{id}', 'UserController@edit')->name('.edit')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update', 'UserController@update')->name('.update')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update/group', 'UserController@update_group')->name('.update-group')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update/{params}', 'UserController@update')->name('.update-password')->middleware(['auth:sanctum', 'verified']);
        Route::post('/delete', 'UserController@delete')->name('.delete')->middleware(['auth:sanctum', 'verified']);
    });

    /*
    * user group
    */ 
    Route::prefix('/user-group')->name('user-group')->group(function() {
        Route::get('/', 'UserGroupController@index')->middleware(['auth:sanctum', 'verified']);
        Route::get('/add', 'UserGroupController@add')->name('.add')->middleware(['auth:sanctum', 'verified']);
        Route::post('/save', 'UserGroupController@save')->name('.save')->middleware(['auth:sanctum', 'verified']);
        Route::get('/edit/{id}', 'UserGroupController@edit')->name('.edit')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update', 'UserGroupController@update')->name('.update')->middleware(['auth:sanctum', 'verified']);
        Route::get('/delete/{id}', 'UserGroupController@delete')->name('.delete')->middleware(['auth:sanctum', 'verified']);
    });
    
    /*
    * profile
    */ 
    Route::prefix('/profile')->name('profile')->group(function() {
        Route::get('/', 'ProfileController@index')->name('.show')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update', 'ProfileController@update')->name('.update')->middleware(['auth:sanctum', 'verified']);
        Route::post('/remove_avatar', 'ProfileController@remove_avatar')->name('.remove-avatar')->middleware(['auth:sanctum', 'verified']);
        Route::get('/change-password', 'ProfileController@change_pass')->name('.change-password')->middleware(['auth:sanctum', 'verified']);
        Route::post('/update-password', 'ProfileController@update_pass')->name('.update-password')->middleware(['auth:sanctum', 'verified']);
        Route::get('/security', 'ProfileController@security')->name('.security')->middleware(['auth:sanctum', 'verified']);
        Route::post('/security/logout-other-devices', 'ProfileController@logout_other_devices')->name('.logout-other-devices')->middleware('ajax');
        Route::post('/security/enable-two-factor', 'ProfileController@enable_two_factor')->name('.enable-two-factor')->middleware('ajax');
        Route::post('/security/show-two-factor-code', 'ProfileController@show_two_factor_code')->name('.show-two-factor-code')->middleware('ajax');
        Route::post('/security/generate-two-factor-code', 'ProfileController@generate_two_factor_code')->name('.generate-two-factor-code')->middleware('ajax');
        Route::post('/security/disable-two-factor', 'ProfileController@disable_two_factor')->name('.disable-two-factor')->middleware('ajax');
        Route::post('/security/confirm-password', 'ProfileController@confirm_password')->name('.confirm-password')->middleware(['ajax']);
    });

});

Route::prefix('/user')->middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/profile', function(){
        return redirect()->route('profile.show');
    });
    Route::get('/confirm-password', function() {
        return redirect()->route('profile.show');
    });
    Route::get('/two-factor-qr-code', function() {
        return redirect()->route('profile.show');
    });
    Route::get('/two-factor-recovery-codes', function() {
        return redirect()->route('profile.show');
    });
    Route::get('/confirmed-password-status', function() {
        return redirect()->route('profile.show');
    });
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
