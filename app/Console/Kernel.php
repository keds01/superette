<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérification des alertes générales (existant)
        $schedule->command('app:check-alerts')->dailyAt('00:00');

        // Vérification des alertes de péremption avec délai personnalisé
        $schedule->command('alertes:peremption')->dailyAt('08:00'); // Exécute tous les jours à 8h du matin

        // Vérification spécifique des alertes de péremption (nouvelle commande)
        $schedule->command('app:check-expiration-alerts')->daily(); // Exécute tous les jours à minuit

        // Vérification des alertes de stock bas
        $schedule->command('app:check-low-stock-alerts')->hourly();

        // Vérification des alertes de mouvements importants
        $schedule->command('app:check-important-movements')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
