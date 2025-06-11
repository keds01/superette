<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerte; // Importer le modèle Alerte
use App\Models\Produit; // Importer le modèle Produit
use App\Services\ProduitService; // Importer le modèle ProduitService
use Illuminate\Support\Facades\Mail; // Importer la façade Mail
use App\Mail\AlertNotification; // Importer la Mailable
use Carbon\Carbon; // Importer Carbon pour les dates

class CheckExpirationAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiration-alerts'; // Renommé pour plus de clarté

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les alertes de péremption et envoie des notifications par email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la vérification des alertes de péremption...');

        // Récupérer les alertes de type 'peremption' qui sont actives et ont un email de notification configuré
        $expirationAlerts = Alerte::where('type', 'peremption')
                                   ->where('actif', true)
                                   ->whereNotNull('notification_email')
                                   ->get();

        if ($expirationAlerts->isEmpty()) {
            $this->info('Aucune alerte de péremption active avec email de notification.');
            return Command::SUCCESS;
        }

        $this->info('' . $expirationAlerts->count() . ' alerte(s) de péremption(s) active(s) trouvée(s).');

        foreach ($expirationAlerts as $alert) {
            $this->info('  -> Vérification de l\'alerte ID: ' . $alert->id . (
                $alert->categorie ? ' (Catégorie: ' . $alert->categorie->nom . ')' : ' (Toutes catégories)')
            );

            // Calculer la date limite basée sur la période de l'alerte
            $expirationThreshold = Carbon::now()->addDays($alert->periode);

            // Construire la requête pour trouver les produits concernés
            $query = Produit::whereNotNull('date_peremption')
                            ->where('date_peremption', '<=', $expirationThreshold)
                            ->where('stock', '<=', $alert->seuil); // Vérifier aussi le seuil de stock

            // Filtrer par catégorie si l'alerte en spécifie une
            if ($alert->categorie_id) {
                $query->where('categorie_id', $alert->categorie_id);
            }

            $matchingProductsCount = $query->count();

            if ($matchingProductsCount > 0) {
                $this->warn('    => Alerte ID ' . $alert->id . ' déclenchée ! (' . $matchingProductsCount . ' produit(s) concerné(s))');

                try {
                    // Envoyer l'email de notification
                    // La Mailable AlertNotification a été conçue pour prendre l'objet Alert
                    Mail::to($alert->notification_email)->send(new AlertNotification($alert));
                    $this->info('    => Email de notification envoyé à ' . $alert->notification_email);

                } catch (\Exception $e) {
                     $this->error('    => Erreur lors de l\'envoi de l\'email pour l\'alerte ID ' . $alert->id . ': ' . $e->getMessage());
                }

            } else {
                $this->info('    => Aucune correspondance trouvée pour l\'alerte ID ' . $alert->id . '.');
            }
        }

        $this->info('Fin de la vérification des alertes de péremption.');

        return Command::SUCCESS;
    }
}
