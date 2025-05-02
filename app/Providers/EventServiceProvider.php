<?php
namespace App\Providers;

use App\Events\Login;
use App\Events\Logout;
use App\Listeners\UpdateUserStatusOnLogin;
use App\Listeners\UpdateUserStatusOnLogout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\UpdateUserStatus::class . '@handleLogin',
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\UpdateUserStatus::class . '@handleLogout',
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
