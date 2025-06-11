<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerte;
use App\Services\ProduitService;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotification;
use Carbon\Carbon;

class CheckImportantMovementsCommand extends Command
{
    protected $signature = 'app:check-important-movements';
    protected $description = 'Vérifie les alertes de mouvements importants et envoie des notifications par email.';

    public function handle()
    {
        $this->info('Début de la vérification des alertes de mouvements importants...');

        $movementAlerts = Alerte::where('type', 'mouvement_important')
            ->where('actif', true)
            ->whereNotNull('notification_email')
            ->get();

        if ($movementAlerts->isEmpty()) {
            $this->info('Aucune alerte de mouvement important active avec email de notification.');
            return Command::SUCCESS;
        }

        $this->info('' . $movementAlerts->count() . ' alerte(s) de mouvement(s) important(s) active(s) trouvée(s).');

        foreach ($movementAlerts as $alert) {
            $this->info('  -> Vérification de l\'alerte ID: ' . $alert->id . (
                $alert->categorie ? ' (Catégorie: ' . $alert->categorie->nom . ')' : ' (Toutes catégories)')
            );

            // Calculer la date de début pour la période spécifiée
            $startDate = Carbon::now()->subDays($alert->periode);

            // Construire la requête pour les mouvements de stock
            $query = MouvementStock::where('created_at', '>=', $startDate)
                ->where(function($q) use ($alert) {
                    $q->where('quantite', '>=', $alert->seuil)
                      ->orWhere('quantite', '<=', -$alert->seuil);
                });

            // Filtrer par catégorie si spécifiée
            if ($alert->categorie_id) {
                $query->whereHas('produit', function($q) use ($alert) {
                    $q->where('categorie_id', $alert->categorie_id);
                });
            }

            $matchingMovementsCount = $query->count();

            if ($matchingMovementsCount > 0) {
                $this->warn('    => Alerte ID ' . $alert->id . ' déclenchée ! (' . $matchingMovementsCount . ' mouvement(s) concerné(s))');

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

        $this->info('Fin de la vérification des alertes de mouvements importants.');
        return Command::SUCCESS;
    }
} 