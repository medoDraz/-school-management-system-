<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => [ 'guest' ]], function(){
		Route::get('/', function () {
			return view('auth.login');
		});
	});

Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath','auth' ]
], function(){

	Route::get('/dashboard', function () {
	    return view('dashboard');
	});

	Route::resource('grade', 'GradeController');

	Route::resource('classrooms', 'ClassroomController');
	Route::post('delete_all', 'ClassroomController@delete_all')->name('delete_all');

    Route::resource('sections', 'SectionController');
    Route::get('/classes/{id}', 'SectionController@getclasses');
});


// Route::get('/', function () {
//     return view('dashboard');
// });



Route::get('/empty', function () {
    return view('empty');
});



Route::get('/home', 'HomeController@index')->name('home');
