<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Auth\\LoginController@index')->name('default');

// auth
Route::get('signup', 'Auth\\RegisterController@index')->name('signup');
Route::post('signup', 'Auth\\RegisterController@store')->name('signup.attempt');
Route::get('login', 'Auth\\LoginController@index')->name('login');
Route::post('login', 'Auth\\LoginController@store')->name('login.attempt');

Route::get('invite/employee/{link}', 'Auth\\UserInvitationController@check');
Route::post('invite/employee/{link}/join', 'Auth\\UserInvitationController@join');
Route::post('invite/employee/{link}/accept', 'Auth\\UserInvitationController@accept');

Route::middleware(['auth'])->group(function () {
    Route::get('logout', 'Auth\\LoginController@logout');

    Route::get('home', 'HomeController@index')->name('home');
    Route::get('settings', 'Settings\\SettingsController@index')->name('settings.index');
    Route::resource('settings/attributes', 'Settings\\AttributesController');
});
