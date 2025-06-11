<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alerte::with(['categorie'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = Categorie::orderBy('nom')->get();
        $alertTypes = [
            'stock_bas' => 'Stock bas',
            'peremption' => 'Péremption',
            'valeur_stock' => 'Valeur du stock',
            'mouvement_important' => 'Mouvement important'
        ];

        return view('alerts.index', compact('alerts', 'categories', 'alertTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:stock_bas,peremption,valeur_stock,mouvement_important',
            'categorie_id' => 'nullable|exists:categories,id',
            'seuil' => 'required|numeric|min:0',
            'periode' => 'required_if:type,peremption,mouvement_important|nullable|integer|min:1',
            'actif' => 'boolean',
            'notification_email' => 'nullable|email'
        ]);

        Alerte::create($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte créée avec succès.');
    }

    public function update(Request $request, Alerte $alert)
    {
        $validated = $request->validate([
            'seuil' => 'required|numeric|min:0',
            'periode' => 'required_if:type,peremption,mouvement_important|nullable|integer|min:1',
            'actif' => 'boolean',
            'notification_email' => 'nullable|email'
        ]);

        try {
            $updateData = $validated;
            if (!in_array($alert->type, ['peremption', 'mouvement_important'])) {
                $updateData['periode'] = null;
            }

            $alert->update($updateData);

            return redirect()->route('alerts.index')
                ->with('success', 'Alerte mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'alerte : ' . $e->getMessage(), ['exception' => $e, 'alert_id' => $alert->id]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'alerte : ' . $e->getMessage());
        }
    }

    public function destroy(Alerte $alert)
    {
        try {
            $alert->delete();

            return redirect()->route('alerts.index')
                ->with('success', 'Alerte supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'alerte : ' . $e->getMessage(), ['exception' => $e, 'alert_id' => $alert->id]);
            return back()->with('error', 'Erreur lors de la suppression de l\'alerte : ' . $e->getMessage());
        }
    }

    public function checkAlerts(): array
    {
        $alerts = Alerte::actif()->get();
        $notifications = [];

        foreach ($alerts as $alert) {
            try {
                switch ($alert->type) {
                    case 'stock_bas':
                        $this->checkStockAlerts($alert, $notifications);
                        break;
                    case 'peremption':
                        $this->checkExpirationAlerts($alert, $notifications);
                        break;
                    case 'valeur_stock':
                        $this->checkStockValueAlerts($alert, $notifications);
                        break;
                    case 'mouvement_important':
                        $this->checkMovementAlerts($alert, $notifications);
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la vérification de l\'alerte (ID: ' . $alert->id . '): ' . $e->getMessage(), ['exception' => $e, 'alert_id' => $alert->id]);
            }
        }

        return $notifications;
    }

    private function checkStockAlerts(Alerte $alert, array &$notifications): void
    {
        $query = Produit::query()
            ->where('stock', '<=', $alert->seuil);

        if ($alert->categorie_id) {
            $query->where('categorie_id', $alert->categorie_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            $notifications[] = [
                'type' => 'stock_bas',
                'message' => "Le stock de {$product->nom} est bas ({$product->stock} unités)",
                'produit_id' => $product->id,
                'alert_id' => $alert->id
            ];
        }
    }

    private function checkExpirationAlerts(Alerte $alert, array &$notifications): void
    {
        $query = MouvementStock::query()
            ->with('produit')
            ->whereNotNull('date_peremption')
            ->where('date_peremption', '<=', now()->addDays($alert->periode))
            ->where('date_peremption', '>', now());

        if ($alert->categorie_id) {
            $query->whereHas('produit', function($q) use ($alert) {
                $q->where('categorie_id', $alert->categorie_id);
            });
        }

        $movements = $query->get();

        foreach ($movements as $movement) {
            $notifications[] = [
                'type' => 'peremption',
                'message' => "Le produit {$movement->produit->nom} (Mvt ID: {$movement->id}) expire dans {$alert->periode} jours (le {$movement->date_peremption->format('d/m/Y')})",
                'product_id' => $movement->produit_id,
                'alert_id' => $alert->id,
                'movement_id' => $movement->id
            ];
        }
    }

    private function checkStockValueAlerts(Alerte $alert, array &$notifications): void
    {
        $query = Produit::query();

        if ($alert->categorie_id) {
            $query->where('categorie_id', $alert->categorie_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            $stockValue = $product->stock * $product->prix_achat_ht;
            if ($stockValue >= $alert->seuil) {
                $notifications[] = [
                    'type' => 'valeur_stock',
                    'message' => "La valeur du stock de {$product->nom} ({$stockValue} FCFA) dépasse le seuil de {$alert->seuil} FCFA",
                    'produit_id' => $product->id,
                    'alert_id' => $alert->id
                ];
            }
        }
    }

    private function checkMovementAlerts(Alerte $alert, array &$notifications): void
    {
        $query = MouvementStock::query()
            ->with('produit')
            ->where('created_at', '>=', now()->subDays($alert->periode))
            ->where('quantite_apres_conditionnement', '>=', $alert->seuil);

        if ($alert->categorie_id) {
            $query->whereHas('produit', function($q) use ($alert) {
                $q->where('categorie_id', $alert->categorie_id);
            });
        }

        $movements = $query->get();

        foreach ($movements as $movement) {
            $notifications[] = [
                'type' => 'mouvement_important',
                'message' => "Mouvement important ({$movement->quantite_apres_conditionnement} unités) pour {$movement->produit->nom} (Mvt ID: {$movement->id}) dans les {$alert->periode} derniers jours.",
                'product_id' => $movement->produit_id,
                'alert_id' => $alert->id,
                'movement_id' => $movement->id
            ];
        }
    }
} 