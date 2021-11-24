<?php

use App\Events\ProgressAddedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('application', \App\Http\Controllers\ApplicationController::class,
    ['only' => ['index', 'store']]);
Route::get('application/free', [\App\Http\Controllers\ApplicationController::class, 'indexFreeApplication']);

Route::resource('group', \App\Http\Controllers\GroupController::class);
Route::post('check_group', [\App\Http\Controllers\GroupController::class, 'checkGroup']);

Route::resource('user', \App\Http\Controllers\UserController::class);
Route::get('/user/group/{id}', [\App\Http\Controllers\UserController::class, 'getUserFromGroup']);
Route::get('/user', [\App\Http\Controllers\UserController::class, 'getAllUser']);
Route::post('/user/search', [\App\Http\Controllers\UserController::class, 'searchUsers']);
Route::post('/users/update-online', [\App\Http\Controllers\UserController::class, 'updateOnline']);
