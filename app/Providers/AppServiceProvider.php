<?php

namespace App\Providers;

use App\Models\Produit;
use App\Observers\ProduitObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

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
        
        // Directive Blade personnalisée pour vérifier une ou plusieurs permissions
        Blade::directive('perm', function ($expression) {
            return "<?php if(auth()->check() && app('Illuminate\\Contracts\\Auth\\Access\\Gate')->any(array_map('trim', explode(',', $expression)))): ?>";
        });
        Blade::directive('endperm', function () {
            return '<?php endif; ?>';
        });
        
        // Enregistrement de l'observateur pour le modèle Produit
        // Cela permettra de créer automatiquement des alertes quand le stock devient bas
        Produit::observe(ProduitObserver::class);
    }
}
