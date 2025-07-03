<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produit;
use App\Models\Alerte;
use Carbon\Carbon;

class VerifierAlertesPeremption extends Command
{
    /**
     * Nom et signature de la commande.
     *
     * @var string
     */
    protected $signature = 'alertes:peremption';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Vérifie et génère automatiquement les alertes pour les produits proches de péremption';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $this->info('Vérification des alertes de péremption...');
        
        // Récupérer tous les produits ayant une date de péremption
        $produits = Produit::whereNotNull('date_peremption')->get();
        
        $compteurAlertes = 0;
        
        foreach ($produits as $produit) {
            // Déterminer le délai d'alerte (personnalisé ou par défaut)
            $delaiAlerte = $produit->delai_alerte_peremption ?? Produit::DELAI_ALERTE_PEREMPTION_DEFAUT;
            
            // Vérifier si le produit est dans la période d'alerte
            $joursRestants = Carbon::now()->diffInDays($produit->date_peremption, false);
            
            // Si le nombre de jours restants est inférieur ou égal au délai d'alerte, mais pas négatif
            if ($joursRestants <= $delaiAlerte && $joursRestants >= 0) {
                // Vérifier si une alerte de péremption existe déjà pour ce produit
                $alerteExistante = Alerte::where('produit_id', $produit->id)
                                         ->where('type', 'peremption')
                                         ->first();
                
                // Récupérer la superette_id du produit
                $superetteId = $produit->superette_id;
                
                // Si le produit n'a pas de superette_id, utiliser la superette par défaut (ID 1)
                if (!$superetteId) {
                    $superetteId = 1; // Superette par défaut
                }
                
                if ($alerteExistante) {
                    // Mettre à jour l'alerte si nécessaire
                    if (!$alerteExistante->estDeclenchee) {
                        $alerteExistante->update([
                            'estDeclenchee' => true,
                            'periode' => $delaiAlerte,
                            'message' => "Le produit {$produit->nom} va expirer dans {$joursRestants} jour(s)",
                            'date_peremption' => $produit->date_peremption,
                            'superette_id' => $alerteExistante->superette_id ?? $superetteId
                        ]);
                        
                        $this->info("✓ Alerte mise à jour pour {$produit->nom} - Expiration dans {$joursRestants} jour(s)");
                        $compteurAlertes++;
                    }
                } else {
                    // Créer une nouvelle alerte de péremption
                    Alerte::create([
                        'produit_id' => $produit->id,
                        'type' => 'peremption',
                        'periode' => $delaiAlerte,
                        'message' => "Le produit {$produit->nom} va expirer dans {$joursRestants} jour(s)",
                        'estDeclenchee' => true,
                        'actif' => true,
                        'date_peremption' => $produit->date_peremption,
                        'superette_id' => $superetteId
                    ]);
                    
                    $this->info("+ Nouvelle alerte créée pour {$produit->nom} - Expiration dans {$joursRestants} jour(s)");
                    $compteurAlertes++;
                }
            } else if ($joursRestants < 0) {
                // Le produit est déjà périmé, vérifier s'il existe une alerte
                $alerteExistante = Alerte::where('produit_id', $produit->id)
                                         ->where('type', 'peremption')
                                         ->first();
                
                // Récupérer la superette_id du produit
                $superetteId = $produit->superette_id;
                
                // Si le produit n'a pas de superette_id, utiliser la superette par défaut (ID 1)
                if (!$superetteId) {
                    $superetteId = 1; // Superette par défaut
                }
                                         
                if ($alerteExistante) {
                    // Mettre à jour l'alerte pour indiquer que le produit est périmé
                    $alerteExistante->update([
                        'estDeclenchee' => true,
                        'message' => "Le produit {$produit->nom} est périmé depuis " . abs($joursRestants) . " jour(s)",
                        'superette_id' => $alerteExistante->superette_id ?? $superetteId
                    ]);
                    
                    $this->info("! Alerte mise à jour pour {$produit->nom} - PÉRIMÉ depuis " . abs($joursRestants) . " jour(s)");
                    $compteurAlertes++;
                } else {
                    // Créer une nouvelle alerte pour un produit périmé
                    Alerte::create([
                        'produit_id' => $produit->id,
                        'type' => 'peremption',
                        'periode' => $delaiAlerte,
                        'message' => "Le produit {$produit->nom} est périmé depuis " . abs($joursRestants) . " jour(s)",
                        'estDeclenchee' => true,
                        'actif' => true,
                        'date_peremption' => $produit->date_peremption,
                        'superette_id' => $superetteId
                    ]);
                    
                    $this->info("! Nouvelle alerte créée pour {$produit->nom} - PÉRIMÉ depuis " . abs($joursRestants) . " jour(s)");
                    $compteurAlertes++;
                }
            }
        }
        
        if ($compteurAlertes > 0) {
            $this->info("Total: {$compteurAlertes} alertes de péremption créées ou mises à jour");
        } else {
            $this->info("Aucune alerte de péremption n'a été déclenchée");
        }
        
        return 0;
    }
}
