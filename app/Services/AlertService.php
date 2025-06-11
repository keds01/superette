<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\Alerte;
use App\Models\HistoriqueAlerte;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlertService
{
    /**
     * Vérifie et génère les alertes de stock pour tous les produits
     */
    public function checkStockAlerts()
    {
        $produits = Produit::all();
        
        foreach ($produits as $produit) {
            $this->checkProductStockAlert($produit);
        }
    }
    
    /**
     * Vérifie et génère les alertes de péremption pour tous les produits
     */
    public function checkExpirationAlerts()
    {
        $produits = Produit::whereNotNull('date_peremption')->get();
        
        foreach ($produits as $produit) {
            $this->checkProductExpirationAlert($produit);
        }
    }
    
    /**
     * Vérifie et génère les alertes de stock pour un produit spécifique
     * Niveaux d'alerte :
     * - Niveau 1 (Jaune) : Stock = 1.5x seuil minimum
     * - Niveau 2 (Rouge) : Stock = seuil minimum
     * - Niveau 3 (Urgent) : Stock insuffisant pour ventes moyennes journalières
     */
    public function checkProductStockAlert(Produit $produit)
    {
        // Calcul de la vente moyenne journalière sur les 30 derniers jours
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        $totalSold = $produit->detailsVente()
            ->whereHas('vente', function($query) use ($startDate, $endDate) {
                $query->whereBetween('date_vente', [$startDate, $endDate]);
            })
            ->sum('quantite');
        
        $averageDailySales = $totalSold / 30;
        
        // Détermination du niveau d'alerte
        $alertType = null;
        $message = '';
        
        if ($averageDailySales > 0 && $produit->stock < $averageDailySales) {
            // Niveau 3 - Urgent : Stock insuffisant pour ventes moyennes journalières
            $alertType = 'stock_urgent';
            $message = "URGENT: Stock critique pour {$produit->nom}. Stock actuel ({$produit->stock}) insuffisant pour couvrir les ventes journalières moyennes ({$averageDailySales}).";
        } elseif ($produit->stock <= $produit->seuil_alerte) {
            // Niveau 2 - Rouge : Stock = seuil minimum
            $alertType = 'stock_bas';
            $message = "ALERTE: Stock bas pour {$produit->nom}. Stock actuel: {$produit->stock}, seuil d'alerte: {$produit->seuil_alerte}.";
        } elseif ($produit->stock <= ($produit->seuil_alerte * 1.5)) {
            // Niveau 1 - Jaune : Stock = 1.5x seuil minimum
            $alertType = 'stock_attention';
            $message = "ATTENTION: Stock en diminution pour {$produit->nom}. Stock actuel: {$produit->stock}, seuil d'alerte: {$produit->seuil_alerte}";
        }
        
        // Création ou mise à jour de l'alerte si nécessaire
        if ($alertType) {
            $existingAlert = Alerte::where('produit_id', $produit->id)
                ->where('type', 'LIKE', 'stock%')
                ->first();
            
            if ($existingAlert) {
                // Mise à jour de l'alerte existante si le niveau a changé
                if ($existingAlert->type !== $alertType) {
                    $existingAlert->update([
                        'type' => $alertType,
                        'message' => $message,
                        'date_resolution' => null
                    ]);
                    
                    // Enregistrement dans l'historique
                    HistoriqueAlerte::create([
                        'alert_id' => $existingAlert->id,
                        'ancien_type' => $existingAlert->type,
                        'nouveau_type' => $alertType,
                        'date_changement' => now()
                    ]);
                }
            } else {
                // Création d'une nouvelle alerte
                $alert = Alerte::create([
                    'type' => $alertType,
                    'produit_id' => $produit->id,
                    'message' => $message,
                    'seuil' => $produit->seuil_alerte
                ]);
                
                // Enregistrement dans l'historique
                HistoriqueAlerte::create([
                    'alert_id' => $alert->id,
                    'nouveau_type' => $alertType,
                    'date_changement' => now()
                ]);
            }
            
            // Ici, on pourrait intégrer l'envoi de SMS via Twilio pour les alertes urgentes
            if ($alertType === 'stock_urgent') {
                $this->sendUrgentSmsNotification($produit, $message);
            }
        } else {
            // Résolution des alertes existantes si le stock est revenu à la normale
            Alerte::where('produit_id', $produit->id)
                ->where('type', 'LIKE', 'stock%')
                ->update(['date_resolution' => now()]);
        }
    }
    
    /**
     * Vérifie et génère les alertes de péremption pour un produit spécifique
     * Alertes:
     * - 15 jours avant: Notification dans l'interface
     * - 5 jours avant: SMS au responsable + mise en avant en caisse
     */
    public function checkProductExpirationAlert(Produit $produit)
    {
        if (!$produit->date_peremption) {
            return;
        }
        
        $daysUntilExpiration = Carbon::now()->diffInDays($produit->date_peremption, false);
        
        // Ignorer les produits déjà périmés
        if ($daysUntilExpiration < 0) {
            return;
        }
        
        $alertType = null;
        $message = '';
        
        if ($daysUntilExpiration <= 5) {
            // Alerte critique - 5 jours ou moins avant péremption
            $alertType = 'peremption_critique';
            $message = "URGENT: Le produit {$product->nom} expire dans {$daysUntilExpiration} jours. Envisager une promotion ou une action immédiate.";
        } elseif ($daysUntilExpiration <= 15) {
            // Alerte normale - 15 jours ou moins avant péremption
            $alertType = 'peremption_proche';
            $message = "ATTENTION: Le produit {$product->nom} expire dans {$daysUntilExpiration} jours.";
        }
        
        // Création ou mise à jour de l'alerte si nécessaire
        if ($alertType) {
            $existingAlert = Alerte::where('produit_id', $produit->id)
                ->where('type', 'LIKE', 'peremption%')
                ->first();
            
            if ($existingAlert) {
                // Mise à jour de l'alerte existante si le niveau a changé
                if ($existingAlert->type !== $alertType) {
                    $existingAlert->update([
                        'type' => $alertType,
                        'message' => $message,
                        'date_resolution' => null,
                        'date_peremption' => $produit->date_peremption
                    ]);
                    
                    // Enregistrement dans l'historique
                    HistoriqueAlerte::create([
                        'alert_id' => $existingAlert->id,
                        'ancien_type' => $existingAlert->type,
                        'nouveau_type' => $alertType,
                        'date_changement' => now()
                    ]);
                }
            } else {
                // Création d'une nouvelle alerte
                $alert = Alerte::create([
                    'type' => $alertType,
                    'produit_id' => $produit->id,
                    'message' => $message,
                    'date_peremption' => $produit->date_peremption
                ]);
                
                // Enregistrement dans l'historique
                HistoriqueAlerte::create([
                    'alert_id' => $alert->id,
                    'nouveau_type' => $alertType,
                    'date_changement' => now()
                ]);
            }
            
            // Envoi de SMS pour les alertes critiques de péremption
            if ($alertType === 'peremption_critique') {
                $this->sendUrgentSmsNotification($produit, $message);
            }
        } else {
            // Résolution des alertes existantes si la date de péremption a été modifiée
            Alerte::where('produit_id', $produit->id)
                ->where('type', 'LIKE', 'peremption%')
                ->update(['date_resolution' => now()]);
        }
    }
    
    /**
     * Envoie une notification SMS urgente (à implémenter avec Twilio)
     */
    private function sendUrgentSmsNotification(Produit $produit, $message)
    {
        try {
            // Ici, on intégrerait l'API Twilio pour envoyer des SMS
            // Pour l'instant, on se contente de logger le message
            if (Log::getLogger()->hasHandlers() && array_key_exists('alerts', config('logging.channels'))) {
                Log::channel('alerts')->info('SMS URGENT à envoyer: ' . $message);
            } else {
                Log::info('SMS URGENT à envoyer: ' . $message);
            }
            
            // Stockage de la notification dans la base de données si possible
            if (class_exists('\App\Models\Notification')) {
                \App\Models\Notification::create([
                    'type' => 'sms_urgent',
                    'message' => $message,
                    'data' => json_encode(['produit_id' => $produit->id]),
                    'status' => 'pending'
                ]);
            }
            
            // Exemple d'intégration Twilio (à décommenter et configurer)
            /*
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $twilioNumber = config('services.twilio.number');
            $recipientNumber = config('services.twilio.recipient'); // Numéro du responsable
            
            if ($sid && $token && $twilioNumber && $recipientNumber) {
                $twilio = new Client($sid, $token);
                $twilio->messages->create(
                    $recipientNumber,
                    [
                        'from' => $twilioNumber,
                        'body' => $message
                    ]
                );
            }
            */
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de notification: ' . $e->getMessage());
        }
    }
}
