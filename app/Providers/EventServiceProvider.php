<?php

namespace App\Providers;

use App\Events\CompanyEvent;
use App\Listeners\CompanyStatusListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CompanyEvent::class => [
            CompanyStatusListener::class
        ]
     ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
