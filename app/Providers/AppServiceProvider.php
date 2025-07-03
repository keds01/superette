<?php

namespace App\Providers;

use App\Models\Produit;
use App\Observers\ProduitObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ajout de la directive Blade isLinkActive pour la navigation
        Blade::if('isLinkActive', function ($routes) {
            return isLinkActive($routes);
        });
        
        // Enregistrement de l'observateur pour le modèle Produit
        // Cela permettra de créer automatiquement des alertes quand le stock devient bas
        Produit::observe(ProduitObserver::class);

        // Injection globale de la superette active
        View::composer('*', function ($view) {
            $activeSuperette = activeSuperette();
            $isAdmin = auth()->check(); // Temporairement, tous les utilisateurs connectés sont considérés comme admin
            $view->with('activeSuperette', $activeSuperette);
            $view->with('isAdmin', $isAdmin);
        });
    }
}
