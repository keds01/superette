<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class ListMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list-middleware {route? : La route spécifique à vérifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liste tous les middlewares enregistrés et les routes avec leurs middlewares';

    /**
     * Execute the console command.
     */
    public function handle(Router $router)
    {
        // Afficher tous les middlewares enregistrés
        $this->info('MIDDLEWARES ENREGISTRÉS:');
        $this->table(
            ['Type', 'Nom'],
            $this->getMiddlewareList($router)
        );

        // Si une route spécifique est fournie
        $routeName = $this->argument('route');
        if ($routeName) {
            $this->info("\nRecherche des informations pour la route: {$routeName}");
            $found = false;
            
            foreach (Route::getRoutes() as $route) {
                if ($route->getName() == $routeName || str_contains($route->uri(), $routeName)) {
                    $this->printRouteInfo($route);
                    $found = true;
                }
            }
            
            if (!$found) {
                $this->error("Aucune route trouvée correspondant à '{$routeName}'");
            }
        } else {
            // Sinon, lister toutes les routes avec leurs middlewares
            $this->info('\nROUTES AVEC MIDDLEWARES:');
            foreach (Route::getRoutes() as $route) {
                if (str_contains($route->uri(), 'produits')) {
                    $this->printRouteInfo($route);
                }
            }
        }
    }

    /**
     * Obtenir la liste des middlewares enregistrés
     */
    protected function getMiddlewareList(Router $router)
    {
        $middlewareGroups = $router->getMiddlewareGroups();
        $middlewareAliases = $router->getMiddleware();
        
        $list = [];
        
        foreach ($middlewareGroups as $name => $middleware) {
            $list[] = ['Groupe', $name];
        }
        
        foreach ($middlewareAliases as $name => $class) {
            $list[] = ['Alias', $name];
        }
        
        return $list;
    }

    /**
     * Afficher les informations d'une route
     */
    protected function printRouteInfo($route)
    {
        $this->info("\nRoute: {$route->uri()}");
        $this->info("Méthodes: " . implode('|', $route->methods()));
        $this->info("Nom: {$route->getName()}");
        $this->info("Action: {$route->getActionName()}");
        $this->info("Middlewares: " . implode(', ', $route->middleware()));
    }
}
