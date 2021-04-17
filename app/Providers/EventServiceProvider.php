<?php

namespace App\Providers;

use App\Events\NewChatStarted;
use App\Events\NewGroupChatCreated;
use App\Events\NewGroupChatMessage;
use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Listeners\SendChatMessageNotification;
use App\Listeners\SendGroupChatMessageNotification;
use App\Listeners\SendNewChatStartedNotification;
use App\Listeners\SendNewGroupChatCreatedNotification;
use App\Listeners\SetUserOffline;
use App\Listeners\SetUserOnline;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            SetUserOnline::class
        ],
        Logout::class => [
            SetUserOffline::class
        ]
        // "App\Events\NewChatMessage" => [
        //     SendChatMessageNotification::class,
        // ],
        // NewChatStarted::class => [
        //     SendNewChatStartedNotification::class,
        // ],
        // NewGroupChatCreated::class => [
        //     SendNewGroupChatCreatedNotification::class,
        // ],
        // NewGroupChatMessage::class => [
        //     SendGroupChatMessageNotification::class,
        // ],
        // UserOnline::class => [

        // ],
        // UserOffline::class => [

        // ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
