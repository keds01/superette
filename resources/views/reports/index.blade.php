@extends('layouts.app')

@section('title', 'Rapports et Analyses')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg flex items-center gap-3">
                    <i class="fas fa-chart-bar text-indigo-400"></i> Rapports & Analyses
                </h1>
                <p class="mt-2 text-lg text-gray-500">Tableaux de bord et statistiques de l'activité commerciale</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour au dashboard
                </a>
                @if(isset($type) && ($type == 'ventes' || $type == 'categories'))
                <a href="#" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-red-500 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
                <a href="#" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-500 to-lime-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Exporter Excel
                </a>
                @endif
            </div>
        </div>

        @if(isset($error_message))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 rounded-xl shadow mb-8 animate-fade-in-down">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle mt-1"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-bold">Une erreur est survenue</p>
                        <p class="text-sm">{{ $error_message }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Sélection de la période -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col md:flex-row flex-wrap gap-6 md:items-end">
                <div class="w-full md:w-auto">
                    <label for="date_debut" class="block text-sm font-medium text-indigo-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" value="{{ $date_debut->format('Y-m-d') }}" 
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="w-full md:w-auto">
                    <label for="date_fin" class="block text-sm font-medium text-indigo-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="{{ $date_fin->format('Y-m-d') }}" 
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="w-full md:w-auto">
                    <label for="type" class="block text-sm font-medium text-indigo-700 mb-1">Type de rapport</label>
                    <select name="type" id="type" 
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="ventes" {{ $type == 'ventes' ? 'selected' : '' }}>Ventes</option>
                        <option value="stock" {{ $type == 'stock' ? 'selected' : '' }}>État du stock</option>
                        <option value="mouvements" {{ $type == 'mouvements' ? 'selected' : '' }}>Mouvements de stock</option>
                        <option value="categories" {{ $type == 'categories' ? 'selected' : '' }}>Analyse par catégorie</option>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" 
                        class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-xl hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i> Générer le rapport
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-indigo-700">Chiffre d'affaires</h3>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($chiffre_affaires ?? 0, 2) }} FCFA</p>
                    <p class="text-xs text-gray-500">Période: {{ $date_debut->format('d/m/Y') }} - {{ $date_fin->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-green-700">Nombre de ventes</h3>
                    <p class="text-2xl font-bold text-green-900">{{ $nombre_ventes ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Transactions terminées</p>
                </div>
            </div>
            <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                    <i class="fas fa-warehouse text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-700">Valeur du stock</h3>
                    <p class="text-2xl font-bold text-yellow-900">{{ number_format($valeur_stock ?? 0, 2) }} FCFA</p>
                    <p class="text-xs text-gray-500">Au prix d'achat HT</p>
                </div>
            </div>
            <div class="bg-gradient-to-tr from-purple-50 to-purple-100 border border-purple-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-purple-700">Marge brute</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($marge_brute ?? 0, 2) }} FCFA</p>
                    <p class="text-xs text-gray-500">Pour la période</p>
                </div>
            </div>
        </div>

        <!-- Contenu du rapport selon le type -->
        @if(isset($type) && $type == 'ventes')
            @include('reports.partials.ventes', [
                'ventes' => $ventes ?? collect(),
                'evolutionVentes' => $evolutionVentes ?? collect(),
                'nombre_ventes' => $nombre_ventes ?? 0,
                'chiffre_affaires' => $chiffre_affaires ?? 0,
                'marge_brute' => $marge_brute ?? 0,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ])
        @elseif(isset($type) && $type == 'stock')
            @include('reports.partials.stock', [
                'produitsEnAlerte' => $produitsEnAlerte ?? 0,
                'produitsEnRupture' => $produitsEnRupture ?? 0,
                'produitsPerimes' => $produitsPerimes ?? 0,
                'produitsPerimesListe' => $produitsPerimesListe ?? collect(),
                'stocks' => $stocks ?? collect(),
                'stockStats' => $stockStats ?? null
            ])
        @elseif(isset($type) && $type == 'mouvements')
            @include('reports.partials.mouvements', [
                'mouvementsGraphique' => $mouvementsGraphique ?? collect(),
                'mouvements' => $mouvements ?? collect()
            ])
        @elseif(isset($type) && $type == 'categories')
            @include('reports.partials.categories', [
                'categories' => $categories ?? collect(),
                'topProduitsParCategorie' => $topProduitsParCategorie ?? []
            ])
        @else
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-12 text-center">
                <i class="fas fa-chart-bar text-5xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-900">Sélectionnez un type de rapport</h3>
                <p class="mt-1 text-sm text-gray-500">Utilisez le formulaire ci-dessus pour générer un rapport spécifique</p>
            </div>
        @endif
    </div>
</div>

@if(!isset($error_message))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuration commune pour les charts
    Chart.defaults.font.family = 'Figtree, sans-serif';
    Chart.defaults.color = '#6B7280';
    Chart.defaults.borderColor = '#E5E7EB';
    
    // Fonction pour initialiser les charts quand nécessaire
    function initCharts() {
        // La fonction sera remplie par les partiels selon le type de rapport actif
        @if(isset($type) && $type == 'ventes' && isset($evolutionVentes) && count($evolutionVentes) > 0)
            if (typeof initVentesChart === 'function') {
                initVentesChart();
            }
        @endif
        
        @if(isset($type) && $type == 'mouvements' && isset($mouvementsGraphique) && count($mouvementsGraphique) > 0)
            if (typeof initMouvementsChart === 'function') {
                initMouvementsChart();
            }
        @endif
        
        @if(isset($type) && $type == 'categories' && isset($categories) && count($categories) > 0)
            if (typeof initCategoriesChart === 'function') {
                initCategoriesChart();
            }
        @endif
    }
    
    // Fonctions pour l'export des données
    function exportToPDF() {
        alert('Fonctionnalité d\'export PDF en cours de développement.');
        // Implémentation à venir avec jsPDF ou via backend
    }
    
    function exportToExcel() {
        alert('Fonctionnalité d\'export Excel en cours de développement.');
        // Implémentation à venir avec SheetJS ou via backend
    }
    
    // Initialisation des charts après chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
    });
</script>
@endif
@endsection