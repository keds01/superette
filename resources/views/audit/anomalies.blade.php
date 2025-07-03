@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-red-600 via-pink-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Détection d'anomalies</h1>
                <p class="mt-2 text-lg text-gray-500">Surveillance des comportements anormaux dans le système</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <a href="{{ route('audit.exporter-anomalies', request()->query()) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-red-500 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('audit.anomalies') }}" method="GET">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="type" class="block text-sm font-medium text-red-700">Type d'anomalie</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-xl border-red-200 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <option value="">Tous les types</option>
                            <option value="variation_prix" {{ request('type') == 'variation_prix' ? 'selected' : '' }}>Variation de prix</option>
                            <option value="annulations_multiples" {{ request('type') == 'annulations_multiples' ? 'selected' : '' }}>Annulations multiples</option>
                            <option value="ajustement_stock_massif" {{ request('type') == 'ajustement_stock_massif' ? 'selected' : '' }}>Ajustement de stock massif</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="severite" class="block text-sm font-medium text-red-700">Sévérité</label>
                        <select name="severite" id="severite" class="mt-1 block w-full rounded-xl border-red-200 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <option value="">Toutes les sévérités</option>
                            <option value="haute" {{ request('severite') == 'haute' ? 'selected' : '' }}>Haute</option>
                            <option value="moyenne" {{ request('severite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="basse" {{ request('severite') == 'basse' ? 'selected' : '' }}>Basse</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-red-700">Date début</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}" 
                                   class="mt-1 block w-full rounded-xl border-red-200 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-red-700">Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}" 
                                   class="mt-1 block w-full rounded-xl border-red-200 focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('audit.anomalies') }}" class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Réinitialiser
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-tr from-red-50 to-red-100 border border-red-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-red-700 font-medium">Anomalies haute sévérité</div>
                <div class="text-3xl font-extrabold text-red-900 mt-2">{{ $anomalies->where('severite', 'haute')->count() }}</div>
            </div>
            
            <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-yellow-700 font-medium">Anomalies moyenne sévérité</div>
                <div class="text-3xl font-extrabold text-yellow-900 mt-2">{{ $anomalies->where('severite', 'moyenne')->count() }}</div>
            </div>
            
            <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-green-700 font-medium">Anomalies basse sévérité</div>
                <div class="text-3xl font-extrabold text-green-900 mt-2">{{ $anomalies->where('severite', 'basse')->count() }}</div>
            </div>
        </div>
        
        <!-- Liste des anomalies -->
        <div class="bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-red-100 bg-red-50/80">
                <h3 class="text-lg font-bold text-red-800">Liste des anomalies détectées</h3>
            </div>
            
            @if(count($anomalies) > 0)
            <ul class="divide-y divide-red-100">
                @foreach($anomalies as $index => $anomalie)
                <li class="hover:bg-red-50/30 transition-colors">
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full flex items-center justify-center
                                    {{ ($anomalie['severite'] ?? 'basse') === 'haute' ? 'bg-gradient-to-tr from-red-500 to-pink-500 text-white' : 
                                      (($anomalie['severite'] ?? 'basse') === 'moyenne' ? 'bg-gradient-to-tr from-yellow-400 to-orange-400 text-white' : 'bg-gradient-to-tr from-green-400 to-teal-400 text-white') }}">
                                        @if(($anomalie['type'] ?? '') === 'variation_prix')
                                            <i class="fas fa-dollar-sign text-xl"></i>
                                        @elseif(($anomalie['type'] ?? '') === 'annulations_multiples')
                                            <i class="fas fa-ban text-xl"></i>
                                        @elseif(($anomalie['type'] ?? '') === 'ajustement_stock_massif')
                                            <i class="fas fa-warehouse text-xl"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-xl"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <div class="text-base font-semibold text-gray-900 flex items-center">
                                        {{ ucfirst(str_replace('_', ' ', $anomalie['type'] ?? 'anomalie')) }}
                                        <span class="ml-3 px-3 py-1 text-xs font-bold rounded-full 
                                        {{ ($anomalie['severite'] ?? 'basse') === 'haute' ? 'bg-red-100 text-red-800' : 
                                          (($anomalie['severite'] ?? 'basse') === 'moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($anomalie['severite'] ?? 'basse') }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ $anomalie['message'] ?? 'Anomalie détectée' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Détecté le {{ now()->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if(isset($anomalie['details']['produit_id']))
                                <a href="{{ route('audit.detail-anomalie', ['type' => $anomalie['type'], 'id' => $anomalie['details']['produit_id']]) }}" 
                                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-red-100 text-red-700 text-sm font-semibold shadow hover:bg-red-200 transition">
                                    <i class="fas fa-search"></i> Voir détails
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            <div class="bg-white px-6 py-4 border-t border-red-100">
                {{ $anomalies->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="h-20 w-20 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <i class="fas fa-check text-green-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune anomalie détectée</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Toutes les opérations semblent normales pour la période sélectionnée. Le système continue de surveiller les activités.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
