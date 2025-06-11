<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Produit::class => \App\Policies\ProduitPolicy::class,
        \App\Models\Vente::class => \App\Policies\VentePolicy::class,
        \App\Models\Stock::class => \App\Policies\StockPolicy::class,
        \App\Models\Commande::class => \App\Policies\CommandePolicy::class,
        \App\Models\Remise::class => \App\Policies\RemisePolicy::class,
        \App\Models\Fournisseur::class => \App\Policies\FournisseurPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
} 