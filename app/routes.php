<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

$p = "uk\\co\\la1tv\\website\\controllers\\";

Route::controller('/admin/media', $p.'home\admin\media\MediaController');
Route::controller('/admin', $p.'home\admin\AdminController');
Route::controller('/', $p.'home\HomeController');