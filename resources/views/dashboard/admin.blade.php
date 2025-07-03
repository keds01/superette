@extends('layouts.app')

@section('content')
    <!-- Header moderne et compact -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 animate-fade-in-down">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 flex items-center gap-3">
                <i class="fas fa-tachometer-alt text-indigo-500 text-3xl md:text-4xl"></i>
                <span>Dashboard <span class="text-indigo-600">Admin</span></span>
            </h1>
            <p class="mt-1 text-base text-gray-500">Bonjour {{ auth()->user()->name }}, synthèse du {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('statistiques.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow hover:shadow-lg hover:-translate-y-1 transition-all">
                <i class="fas fa-chart-bar"></i> Statistiques
            </a>
            <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 text-white font-bold shadow hover:shadow-lg hover:-translate-y-1 transition-all">
                <i class="fas fa-shield-alt"></i> Audit
            </a>
        </div>
    </div>

    <!-- Cartes statistiques XXL -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
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
                <i class="fas fa-store text-3xl text-purple-500"></i>
                <span class="text-lg font-semibold text-purple-700">Superettes</span>
            </div>
            <div class="text-4xl font-extrabold text-purple-900">{{ $stats['totalSuperettes'] ?? 0 }}</div>
        </div>
        <div class="bg-white border-l-8 border-red-500 rounded-2xl shadow-xl p-7 flex flex-col items-start hover:scale-105 transition-transform">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                <span class="text-lg font-semibold text-red-700">Alertes produits</span>
            </div>
            <div class="text-4xl font-extrabold text-red-900">{{ $stats['produitsSousSeuilAlerte'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Timeline des dernières ventes et mouvements -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Timeline ventes -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Dernières ventes</h3>
            <ul class="space-y-4">
                @foreach($dernieresVentes as $vente)
                    <li class="flex items-center gap-4">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100">
                            <i class="fas fa-shopping-cart text-indigo-600"></i>
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900">{{ $vente->superette->nom }}</span>
                                <span class="text-xs text-gray-500">{{ $vente->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-sm text-gray-700">Montant : <span class="font-bold">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</span></div>
                            <div>
                                @if($vente->statut == 'terminee' || $vente->statut == 'completee')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $vente->statut }}</span>
                                @elseif($vente->statut == 'annulee')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $vente->statut }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $vente->statut }}</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <!-- Timeline mouvements -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Derniers mouvements de stock</h3>
            <ul class="space-y-4">
                @foreach($derniersMouvements as $mouvement)
                    <li class="flex items-center gap-4">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                            <i class="fas fa-exchange-alt text-green-600"></i>
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900">{{ $mouvement->produit->nom }}</span>
                                <span class="text-xs text-gray-500">{{ $mouvement->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-sm text-gray-700">Superette : <span class="font-bold">{{ $mouvement->superette->nom }}</span></div>
                            <div>
                                @if($mouvement->type == 'entree')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Entrée</span>
                                @elseif($mouvement->type == 'sortie')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Sortie</span>
                                @elseif($mouvement->type == 'ajustement_positif')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Ajustement +</span>
                                @elseif($mouvement->type == 'ajustement_negatif')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ajustement -</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $mouvement->type }}</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Actions rapides ergonomiques -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <a href="{{ route('superettes.create') }}" class="bg-gradient-to-tr from-green-500 to-emerald-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-store-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Nouvelle Superette</h3>
                        <p class="text-sm text-white/80">Ajouter une superette</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="{{ route('users.create') }}" class="bg-gradient-to-tr from-blue-500 to-indigo-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Nouvel Utilisateur</h3>
                        <p class="text-sm text-white/80">Ajouter un utilisateur</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="{{ route('reports.index') }}" class="bg-gradient-to-tr from-purple-500 to-pink-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Rapports</h3>
                        <p class="text-sm text-white/80">Voir les rapports</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="{{ route('audit.index') }}" class="bg-gradient-to-tr from-red-500 to-orange-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Audit</h3>
                        <p class="text-sm text-white/80">Journal d'activités</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
@endsection
