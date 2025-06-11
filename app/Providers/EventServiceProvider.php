<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Ajoutez vos événements ici si besoin
    ];

    public function boot(): void
    {
        //
    }
} 