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

Route::prefix('users')->middleware('auth:api')->group(function () {
    Route::put('online/{user}', 'Api\UserController@updateUserOnline');
    Route::put('offline/{user}', 'Api\UserController@updateUserOffline');
});

Route::prefix('/apps/chat')->middleware('auth:api')->group(function () {
    // Private chats
    Route::post('msg', 'Api\ChatController@storeMsg');
    Route::get('contacts', 'Api\ChatController@getContacts');
    Route::get('chats', 'Api\ChatController@getChats');
    Route::get('chat-contacts', 'Api\ChatController@getChatContacts');
    Route::post('mark-all-seen', 'Api\ChatController@markAllSeen');
    Route::post('set-pinned', 'Api\ChatController@setPinned');

    // Group chats
    Route::post('groups/msg', 'Api\GroupChatController@storeGroupChatMsg');
    Route::get('groups', 'Api\GroupChatController@getChatGroups');
    Route::post('groups', 'Api\GroupChatController@storeGroup');
    Route::get('groups/{groupChat}', 'Api\GroupChatController@getGroupChatMessages');
});

Route::prefix('/notifications')->middleware('auth:api')->group(function () {
    Route::post('/', 'Api\NotificationController@storeNotification');
    Route::get('/', 'Api\NotificationController@getNotifications');
});
