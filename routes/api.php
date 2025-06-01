<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeLineController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});

Route::get('testview', [APIController::class, 'testView']);

Route::post('/login', [APIController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
	Route::get('/list-user', [APIController::class, 'listUser']);
	Route::post('/user-store', [APIController::class, 'store']);
	Route::patch('/update-password', [APIController::class, 'updatePassword']);
	Route::delete('/delete-user/{id}', [APIController::class, 'deleteUser']);

	Route::get('/my-infor', [UserController::class, 'show']);
	Route::get('/list-suggest-friend', [UserController::class, 'suggestFriend']);
	Route::get('/add-friend', [UserController::class, 'addFriend']);
	Route::get('/list-request-friend', [UserController::class, 'listFriendRequest']);
	Route::get('/accept', [UserController::class, 'accept']);
	Route::get('/most-followed', [UserController::class, 'mostFollowed']);
	Route::get('/search', [UserController::class, 'search']);
	Route::get('/setDeviceToken', [UserController::class, 'setDeviceToken']);
	Route::post('/create-post', [UserController::class, 'store']);
	Route::get('/timeline', [TimeLineController::class, 'timeline']);
	Route::get('/add-favorites', [TimeLineController::class, 'addFavorite']);
	Route::get('/remove-favorites', [TimeLineController::class, 'removeFavorite']);
	Route::get('/like', [TimeLineController::class, 'like']);
	Route::get('list-comment', [TimeLineController::class, 'showComment']);
	Route::post('/comment', [TimeLineController::class, 'postComment']);
	Route::get('/list-friend', [UserController::class, 'listFriend']);
	Route::get('/list-message', [UserController::class, 'listMessage']);
	Route::post('/send-message', [UserController::class, 'sendMessage']);
});

Route::post('/register', [AuthController::class, 'register']);
