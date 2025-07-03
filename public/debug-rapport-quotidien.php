<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Employe;
use App\Models\Paiement;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

echo "<h1>Debug Rapport Quotidien - Génération de données de test</h1>";

try {
    // Vérifier les données existantes
    $ventes = Vente::count();
    $produits = Produit::count();
    $clients = Client::count();
    $employes = Employe::count();
    
    echo "<h2>Données existantes</h2>";
    echo "<ul>";
    echo "<li>Ventes: {$ventes}</li>";
    echo "<li>Produits: {$produits}</li>";
    echo "<li>Clients: {$clients}</li>";
    echo "<li>Employés: {$employes}</li>";
    echo "</ul>";
    
    if ($produits === 0 || $employes === 0) {
        echo "<p style='color: red;'>Impossible de créer des ventes: produits ou employés manquants.</p>";
        exit;
    }
    
    // Créer des ventes pour aujourd'hui
    $aujourdhui = Carbon::today();
    $ventesAujourdhui = Vente::whereDate('created_at', $aujourdhui)->count();
    
    if ($ventesAujourdhui === 0) {
        echo "<h2>Création de ventes pour aujourd'hui</h2>";
        
        $produitsDisponibles = Produit::where('stock', '>', 0)->take(5)->get();
        $employesDisponibles = Employe::take(2)->get();
        $clientsDisponibles = Client::take(3)->get();
        
        if ($produitsDisponibles->isEmpty()) {
            echo "<p style='color: red;'>Aucun produit avec du stock disponible.</p>";
            exit;
        }
        
        $ventesCreees = 0;
        
        for ($i = 0; $i < 5; $i++) {
            $employe = $employesDisponibles->random();
            $client = $clientsDisponibles->random();
            $statut = ['completee', 'terminee', 'annulee'][array_rand(['completee', 'terminee', 'annulee'])];
            
            // Créer la vente
            $vente = Vente::create([
                'numero_vente' => 'V' . date('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'client_id' => $client->id,
                'employe_id' => $employe->id,
                'date_vente' => $aujourdhui->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                'type_vente' => ['sur_place', 'a_emporter'][array_rand(['sur_place', 'a_emporter'])],
                'montant_total' => 0, // Sera calculé après
                'montant_paye' => 0,
                'montant_restant' => 0,
                'statut' => $statut,
                'notes' => 'Vente de test générée automatiquement'
            ]);
            
            // Ajouter des produits à la vente
            $montantTotal = 0;
            $nbProduits = rand(1, 3);
            
            for ($j = 0; $j < $nbProduits; $j++) {
                $produit = $produitsDisponibles->random();
                $quantite = rand(1, min(5, $produit->stock));
                $prixUnitaire = $produit->prix_vente_ttc;
                $sousTotal = $quantite * $prixUnitaire;
                
                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produit->id,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'prix_achat_unitaire' => $produit->prix_achat_ht ?? 0,
                    'sous_total' => $sousTotal
                ]);
                
                $montantTotal += $sousTotal;
                
                // Mettre à jour le stock
                $produit->decrement('stock', $quantite);
            }
            
            // Mettre à jour le montant total de la vente
            $vente->update([
                'montant_total' => $montantTotal,
                'montant_paye' => $statut === 'annulee' ? 0 : $montantTotal,
                'montant_restant' => $statut === 'annulee' ? 0 : 0
            ]);
            
            // Créer un paiement si la vente n'est pas annulée
            if ($statut !== 'annulee') {
                Paiement::create([
                    'vente_id' => $vente->id,
                    'mode_paiement' => ['especes', 'carte', 'mobile_money'][array_rand(['especes', 'carte', 'mobile_money'])],
                    'montant' => $montantTotal,
                    'reference_paiement' => 'REF' . time() . rand(100, 999),
                    'statut' => 'valide',
                    'date_paiement' => $vente->date_vente
                ]);
            }
            
            $ventesCreees++;
        }
        
        echo "<p>✓ {$ventesCreees} ventes créées pour aujourd'hui</p>";
    } else {
        echo "<p>Des ventes existent déjà pour aujourd'hui ({$ventesAujourdhui} ventes)</p>";
    }
    
    // Vérifier les activités
    $activitesAujourdhui = ActivityLog::whereDate('created_at', $aujourdhui)->count();
    if ($activitesAujourdhui === 0) {
        echo "<h2>Création d'activités pour aujourd'hui</h2>";
        
        $users = User::all();
        if ($users->isEmpty()) {
            echo "<p style='color: red;'>Aucun utilisateur trouvé.</p>";
        } else {
            $activitesCreees = 0;
            $types = ['connexion', 'modification', 'creation', 'consultation'];
            
            for ($i = 0; $i < 10; $i++) {
                $user = $users->random();
                $type = $types[array_rand($types)];
                $date = $aujourdhui->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59));
                
                ActivityLog::create([
                    'type' => $type,
                    'description' => "Activité de test: {$type}",
                    'user_id' => $user->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'metadata' => json_encode(['test' => true]),
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
                
                $activitesCreees++;
            }
            
            echo "<p>✓ {$activitesCreees} activités créées pour aujourd'hui</p>";
        }
    } else {
        echo "<p>Des activités existent déjà pour aujourd'hui ({$activitesAujourdhui} activités)</p>";
    }
    
    // Statistiques finales
    echo "<h2>Statistiques finales pour aujourd'hui</h2>";
    $ventesFinales = Vente::whereDate('created_at', $aujourdhui)->count();
    $paiementsFinaux = Paiement::whereDate('created_at', $aujourdhui)->count();
    $activitesFinales = ActivityLog::whereDate('created_at', $aujourdhui)->count();
    
    echo "<ul>";
    echo "<li>Ventes: {$ventesFinales}</li>";
    echo "<li>Paiements: {$paiementsFinaux}</li>";
    echo "<li>Activités: {$activitesFinales}</li>";
    echo "</ul>";
    
    echo "<h2>Test du rapport quotidien</h2>";
    echo "<p><a href='/audit/rapport-quotidien' target='_blank'>Cliquer ici pour voir le rapport quotidien</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}
?> 