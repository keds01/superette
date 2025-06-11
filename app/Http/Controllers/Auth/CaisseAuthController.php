<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class CaisseAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Appliquer le middleware 'guest' uniquement à la vue du formulaire de connexion
        // pour rediriger les utilisateurs déjà authentifiés.
        // $this->middleware('guest:caisse')->except('logout'); // Désactivé pour retirer l'authentification sur cette page
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('caisse.login'); // Assurez-vous que cette vue existe
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Tenter l'authentification en utilisant le guard 'caisse'
        if (Auth::guard('caisse')->attempt($credentials, $request->boolean('remember')))
        {
            $request->session()->regenerate();

            // Rediriger l'utilisateur après une connexion réussie
            return redirect()->intended(route('caisse.dashboard')); // Rediriger vers le tableau de bord de la caisse
        }

        // Si l'authentification échoue, retourner au formulaire avec les erreurs
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('caisse')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Rediriger vers la page d'accueil après la déconnexion
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('caisse.dashboard'); // Assurez-vous que cette vue existe
    }

    /**
     * Check if the user session is active.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSession()
    {
         if (Auth::guard('caisse')->check()) {
             return response()->json(['status' => 'active']);
         }
         return response()->json(['status' => 'inactive'], 401);
    }
} 