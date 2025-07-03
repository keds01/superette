@extends('layouts.app')

@section('content')
    @if(auth()->user()->isCaissier())
        <!-- Header moderne caissier -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 animate-fade-in-down">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 flex items-center gap-3">
                    <i class="fas fa-cash-register text-green-500 text-3xl md:text-4xl"></i>
                    <span>Dashboard <span class="text-green-600">Caissier</span></span>
                </h1>
                <p class="mt-1 text-base text-gray-500">Bienvenue {{ auth()->user()->name }}, prêt à encaisser ?</p>
            </div>
        </div>
        <!-- Cartes statistiques caissier (optionnel) -->
        <!-- Actions rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('ventes.create') }}" class="bg-gradient-to-tr from-green-500 to-emerald-600 p-7 rounded-2xl shadow-xl hover:scale-105 transition-transform text-white flex items-center gap-4">
                            <i class="fas fa-cash-register text-3xl"></i>
                        <div>
                            <h3 class="font-semibold text-xl">Nouvelle Vente</h3>
                            <p class="text-white/80">Enregistrer une nouvelle vente</p>
                        </div>
                </a>
            <a href="{{ route('clients.index') }}" class="bg-gradient-to-tr from-blue-500 to-indigo-600 p-7 rounded-2xl shadow-xl hover:scale-105 transition-transform text-white flex items-center gap-4">
                            <i class="fas fa-users text-3xl"></i>
                        <div>
                            <h3 class="font-semibold text-xl">Clients</h3>
                            <p class="text-white/80">Gérer les clients</p>
                        </div>
                </a>
            </div>
        <!-- Accès rapides -->
        <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Accès rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('ventes.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-list text-indigo-600 mr-3"></i>
                        <span>Liste des ventes</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-user-plus text-indigo-600 mr-3"></i>
                        <span>Nouveau client</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-user-cog text-indigo-600 mr-3"></i>
                        <span>Mon profil</span>
                    </a>
            </div>
        </div>
    @elseif(auth()->user()->isResponsable() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
        <!-- Header moderne responsable -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 animate-fade-in-down">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 flex items-center gap-3">
                    <i class="fas fa-tachometer-alt text-indigo-500 text-3xl md:text-4xl"></i>
                    <span>Dashboard <span class="text-indigo-600">Responsable</span></span>
                </h1>
                <p class="mt-1 text-base text-gray-500">Bonjour {{ auth()->user()->name }}, synthèse du {{ now()->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('statistiques.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow hover:shadow-lg hover:-translate-y-1 transition-all">
                    <i class="fas fa-chart-bar"></i> Statistiques
                </a>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 text-white font-bold shadow hover:shadow-lg hover:-translate-y-1 transition-all">
                    <i class="fas fa-user-cog"></i> Mon profil
                </a>
            </div>
        </div>
        <!-- Cartes statistiques XXL responsable -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white border-l-8 border-indigo-500 rounded-2xl shadow-xl p-7 flex flex-col items-start hover:scale-105 transition-transform">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-money-bill-wave text-3xl text-indigo-500"></i>
                    <span class="text-lg font-semibold text-indigo-700">Chiffre d'affaires</span>
                </div>
                <div class="text-4xl font-extrabold text-indigo-900">{{ number_format($stats['chiffreAffaires'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="bg-white border-l-8 border-green-500 rounded-2xl shadow-xl p-7 flex flex-col items-start hover:scale-105 transition-transform">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-coins text-3xl text-green-500"></i>
                    <span class="text-lg font-semibold text-green-700">Valeur du stock</span>
                </div>
                <div class="text-4xl font-extrabold text-green-900">{{ number_format($stats['valeurStock'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="bg-white border-l-8 border-purple-500 rounded-2xl shadow-xl p-7 flex flex-col items-start hover:scale-105 transition-transform">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-box text-3xl text-purple-500"></i>
                    <span class="text-lg font-semibold text-purple-700">Total produits</span>
                </div>
                <div class="text-4xl font-extrabold text-purple-900">{{ $stats['totalProduits'] ?? 0 }}</div>
            </div>
            <div class="bg-white border-l-8 border-red-500 rounded-2xl shadow-xl p-7 flex flex-col items-start hover:scale-105 transition-transform">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                    <span class="text-lg font-semibold text-red-700">En alerte</span>
                </div>
                <div class="text-4xl font-extrabold text-red-900">{{ $stats['produitsSousSeuilAlerte'] ?? 0 }}</div>
            </div>
        </div>
        <!-- Notifications stylées -->
        @if(!empty($notifications))
            <div class="mb-8">
                <div class="rounded-lg bg-yellow-50 p-6 shadow flex items-start gap-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-400"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Alertes importantes</h3>
                        <ul class="list-disc pl-5 text-yellow-700 space-y-1">
                                    @foreach($notifications as $notification)
                                        <li>{{ $notification['message'] }}</li>
                                    @endforeach
                                </ul>
                    </div>
                </div>
            </div>
        @endif
        <!-- Actions rapides responsable -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('ventes.create') }}" class="bg-gradient-to-tr from-green-500 to-emerald-600 p-5 rounded-xl shadow-lg hover:scale-105 transition-transform text-white flex items-center gap-3">
                        <i class="fas fa-cash-register text-2xl"></i>
                <span>Nouvelle Vente</span>
            </a>
            <a href="{{ route('produits.create') }}" class="bg-gradient-to-tr from-blue-500 to-indigo-600 p-5 rounded-xl shadow-lg hover:scale-105 transition-transform text-white flex items-center gap-3">
                        <i class="fas fa-box-open text-2xl"></i>
                <span>Ajouter Produit</span>
            </a>
            <a href="{{ route('alertes.index') }}" class="bg-gradient-to-tr from-red-500 to-pink-600 p-5 rounded-xl shadow-lg hover:scale-105 transition-transform text-white flex items-center gap-3">
                        <i class="fas fa-bell text-2xl"></i>
                <span>Alertes</span>
            </a>
            <a href="{{ route('clients.create') }}" class="bg-gradient-to-tr from-purple-500 to-pink-600 p-5 rounded-xl shadow-lg hover:scale-105 transition-transform text-white flex items-center gap-3">
                <i class="fas fa-user-plus text-2xl"></i>
                <span>Nouveau Client</span>
            </a>
        </div>
        <!-- Statistiques principales responsable (optionnel) -->
        <!-- ... (tu peux ajouter une timeline ou d'autres blocs ici si besoin) ... -->
    @endif
@endsection 
