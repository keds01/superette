<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlertService;

class CheckAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie et génère les alertes de stock et de péremption';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alertService)
    {
        $this->info('Vérification des alertes de stock...');
        $alertService->checkStockAlerts();
        
        $this->info('Vérification des alertes de péremption...');
        $alertService->checkExpirationAlerts();
        
        $this->info('Vérification des alertes terminée.');
        
        return Command::SUCCESS;
    }
}
