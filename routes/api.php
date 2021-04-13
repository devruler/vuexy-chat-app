<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('/apps/chat')->middleware('auth:api')->group(function () {
    Route::post('msg', 'Api\ChatController@storeMsg');
    Route::get('contacts', 'Api\ChatController@getContacts');
    Route::get('chats', 'Api\ChatController@getChats');
    Route::get('chat-contacts', 'Api\ChatController@getChatContacts');
    Route::post('mark-all-seen', 'Api\ChatController@markAllSeen');
    Route::post('set-pinned', 'Api\ChatController@setPinned');


});

Route::prefix('/notifications')->middleware('auth:api')->group(function () {
    Route::post('/', 'Api\NotificationController@storeNotification');
    Route::get('/', 'Api\NotificationController@getNotifications');
});
