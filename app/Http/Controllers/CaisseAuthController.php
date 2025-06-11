<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;

class CaisseAuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion à la caisse
     */
    public function showLoginForm()
    {
        // Check if a caisse session already exists, redirect to dashboard if true
        if (Session::has('caisse_id')) {
            return redirect()->route('caisse.dashboard');
        }
        return View::make('caisse.auth.login');
    }

    /**
     * Authentifie l'utilisateur de la caisse
     */
    public function login(Request $request)
    {
        $request->validate([
            'numero_caisse' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $caisse = Caisse::where('numero', $request->numero_caisse)
                ->where('is_active', true)
                ->first();

            if (!$caisse || !Hash::check($request->password, $caisse->password)) {
                Log::warning('Tentative de connexion caisse échouée (identifiants incorrects)', [
                    'numero_caisse' => $request->numero_caisse,
                    'ip' => $request->ip(),
                ]);
                return back()->withErrors([
                    'numero_caisse' => 'Les identifiants sont incorrects.',
                ])->withInput();
            }

            // Stocker les informations de la caisse en session
            Session::put('caisse_id', $caisse->id);
            Session::put('caisse_numero', $caisse->numero);
            Session::put('caisse_nom', $caisse->nom);

            Log::info('Connexion caisse réussie', [
                'caisse_id' => $caisse->id,
                'numero' => $caisse->numero,
                'user_id' => Auth::id(), // Log the currently authenticated user if applicable
                'ip' => $request->ip(),
            ]);

            return redirect()->route('caisse.dashboard');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion à la caisse', [
                'error' => $e->getMessage(),
                'numero_caisse' => $request->numero_caisse,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'numero_caisse' => 'Une erreur est survenue lors de la connexion. Veuillez réessayer.',
            ])->withInput();
        }
    }

    /**
     * Déconnecte l'utilisateur de la caisse
     */
    public function logout()
    {
        $caisseId = Session::get('caisse_id');
        $caisseNumero = Session::get('caisse_numero');

        // Clear the caisse session data
        Session::forget(['caisse_id', 'caisse_numero', 'caisse_nom']);

        if ($caisseId) {
             Log::info('Déconnexion caisse réussie', [
                'caisse_id' => $caisseId,
                'numero' => $caisseNumero,
                'user_id' => Auth::id(), // Log the currently authenticated user if applicable
             ]);
        }

        return redirect()->route('caisse.login')
                        ->with('success', 'Vous avez été déconnecté de la caisse.');
    }

    /**
     * Affiche le tableau de bord de la caisse
     */
    public function dashboard()
    {
        $caisseId = Session::get('caisse_id');

        if (!$caisseId) {
            // If no caisse session, redirect to login
            Log::warning('Accès non autorisé au tableau de bord caisse (session manquante)', [
                'ip' => request()->ip(),
            ]);
            return redirect()->route('caisse.login');
        }

        try {
            $caisse = Caisse::with(['ventes' => function ($query) {
                $query->whereDate('created_at', Carbon::today())
                    ->orderBy('created_at', 'desc')
                    ->take(5);
            }])->findOrFail($caisseId);

            $stats = [
                'ventes_jour' => $caisse->ventes()
                    ->whereDate('created_at', Carbon::today())
                    ->count(),
                'montant_jour' => $caisse->ventes()
                    ->whereDate('created_at', Carbon::today())
                    ->sum('montant_total'),
                'dernieres_ventes' => $caisse->ventes
            ];

            return View::make('caisse.dashboard', compact('caisse', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'accès au tableau de bord caisse', [
                'error' => $e->getMessage(),
                'caisse_id' => $caisseId,
                'ip' => request()->ip(),
            ]);
            // Clear session on error to prevent loop if caisse is not found
            Session::forget(['caisse_id', 'caisse_numero', 'caisse_nom']);
            return redirect()->route('caisse.login')
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'accès au tableau de bord.']);
        }
    }

    /**
     * Vérifie si la caisse est connectée via AJAX/API
     */
    public function checkSession()
    {
        if (!Session::has('caisse_id')) {
            return response()->json(['authenticated' => false]);
        }

        $caisse = Caisse::find(Session::get('caisse_id'));

        if (!$caisse) {
             // Clear session if caisse not found in DB
            Session::forget(['caisse_id', 'caisse_numero', 'caisse_nom']);
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'caisse' => [
                'id' => $caisse->id,
                'numero' => $caisse->numero,
                'nom' => $caisse->nom
            ]
        ]);
    }
} 