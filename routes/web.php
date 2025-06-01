<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\APIController;
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

Route::get('/login', function () {
	$apiRes = new ApiResponse();
	return $apiRes->UnAuthorization();
})->name('login');

Route::get('/un-lock/{hashId}', [APIController::class, 'unlock']);
