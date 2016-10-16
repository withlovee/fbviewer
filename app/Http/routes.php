<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', function () {
// $manager = new MongoDB\Driver\Manager("mongodb://localhost");
	// return phpinfo();
    // return view('welcome');ongo
// });

Route::get('/', 'CommentController@index');

Route::post('/delete', 'CommentController@delete');
