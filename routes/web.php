<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Legacy Routes
Route::group(['prefix' => 'legacy'], function () {
    Route::get('index.php', 'LegacyController@index');
    Route::post('login.php', 'LegacyController@login');
    Route::get('mainMenu.php', 'LegacyController@mainMenu');
    Route::get('Database.php', 'LegacyController@Database');
    Route::get('changePassword.php', 'LegacyController@changePassword');
    Route::get('deleteEmployee.php', 'LegacyController@deleteEmployee');
    Route::get('editEmployee.php', 'LegacyController@editEmployee');
    Route::get('deleteRoom.php', 'LegacyController@deleteRoom');
    Route::get('editRoom.php', 'LegacyController@editRoom');
    Route::get('EmployeeCard.php', 'LegacyController@EmployeeCard');
    Route::get('EmployeesList.php', 'LegacyController@EmployeesList');
    Route::get('RoomCard.php', 'LegacyController@RoomCard');
    Route::get('RoomsList.php', 'LegacyController@RoomsList');
    Route::get('updateEmployee.php', 'LegacyController@updateEmployee');
    Route::get('updateRoom.php', 'LegacyController@updateRoom');
});