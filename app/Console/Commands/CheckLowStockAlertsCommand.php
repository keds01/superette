<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerte;
use App\Services\ProduitService;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotification;
use Carbon\Carbon;

class CheckLowStockAlertsCommand extends Command
{
    protected $signature = 'app:check-low-stock-alerts';
    protected $description = 'Vérifie les alertes de stock bas et envoie des notifications par email.';

    public function handle()
    {
        $this->info('Début de la vérification des alertes de stock bas...');

        $lowStockAlerts = Alerte::where('type', 'stock_bas')
            ->where('actif', true)
            ->whereNotNull('notification_email')
            ->get();

        if ($lowStockAlerts->isEmpty()) {
            $this->info('Aucune alerte de stock bas active avec email de notification.');
            return Command::SUCCESS;
        }

        $this->info('' . $lowStockAlerts->count() . ' alerte(s) de stock bas active(s) trouvée(s).');

        foreach ($lowStockAlerts as $alert) {
            $this->info('  -> Vérification de l\'alerte ID: ' . $alert->id . (
                $alert->categorie ? ' (Catégorie: ' . $alert->categorie->nom . ')' : ' (Toutes catégories)')
            );

            $query = \App\Models\Produit::where('stock', '<=', $alert->seuil);

            if ($alert->categorie_id) {
                $query->where('categorie_id', $alert->categorie_id);
            }

            $matchingProductsCount = $query->count();

            if ($matchingProductsCount > 0) {
                $this->warn('    => Alerte ID ' . $alert->id . ' déclenchée ! (' . $matchingProductsCount . ' produit(s) concerné(s))');

                try {
                    Mail::to($alert->notification_email)->send(new AlertNotification($alert));
                    $this->info('    => Email de notification envoyé à ' . $alert->notification_email);
                } catch (\Exception $e) {
                    $this->error('    => Erreur lors de l\'envoi de l\'email pour l\'alerte ID ' . $alert->id . ': ' . $e->getMessage());
                }
            } else {
                $this->info('    => Aucune correspondance trouvée pour l\'alerte ID ' . $alert->id . '.');
            }
        }

        $this->info('Fin de la vérification des alertes de stock bas.');
        return Command::SUCCESS;
    }
} 