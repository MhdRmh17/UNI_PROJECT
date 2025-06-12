<?php

namespace App\Providers;

use Illuminate\Broadcasting\BroadcastServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
       
    }
}
