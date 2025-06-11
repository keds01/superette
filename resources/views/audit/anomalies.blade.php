@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Détection d'anomalies</h1>
            <div>
                <a href="{{ route('audit.exporter-anomalies', request()->query()) }}" class="btn-primary">
                    <i class="fas fa-file-export mr-2"></i>Exporter PDF
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form action="{{ route('audit.anomalies') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type d'anomalie</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Tous les types</option>
                                <option value="prix_variation" {{ request('type') == 'prix_variation' ? 'selected' : '' }}>Variation de prix</option>
                                <option value="stock_discrepancy" {{ request('type') == 'stock_discrepancy' ? 'selected' : '' }}>Écart de stock</option>
                                <option value="vente_annulation" {{ request('type') == 'vente_annulation' ? 'selected' : '' }}>Annulation de vente</option>
                                <option value="caisse_discrepancy" {{ request('type') == 'caisse_discrepancy' ? 'selected' : '' }}>Écart de caisse</option>
                                <option value="login_failure" {{ request('type') == 'login_failure' ? 'selected' : '' }}>Échec de connexion</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="severite" class="block text-sm font-medium text-gray-700">Sévérité</label>
                            <select name="severite" id="severite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Toutes les sévérités</option>
                                <option value="haute" {{ request('severite') == 'haute' ? 'selected' : '' }}>Haute</option>
                                <option value="moyenne" {{ request('severite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                <option value="basse" {{ request('severite') == 'basse' ? 'selected' : '' }}>Basse</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Tous les statuts</option>
                                <option value="nouvelle" {{ request('status') == 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                                <option value="en_cours" {{ request('status') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="resolue" {{ request('status') == 'resolue' ? 'selected' : '' }}>Résolue</option>
                                <option value="ignoree" {{ request('status') == 'ignoree' ? 'selected' : '' }}>Ignorée</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date début</label>
                                <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date fin</label>
                                <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('audit.anomalies') }}" class="btn-secondary">
                            Réinitialiser
                        </a>
                        <button type="submit" class="btn-primary">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Liste des anomalies -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if(count($anomalies) > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($anomalies as $anomalie)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full 
                                    {{ $anomalie['severite'] === 'haute' ? 'bg-red-100 text-red-700' : 
                                      ($anomalie['severite'] === 'moyenne' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        @if($anomalie['type'] === 'prix_variation')
                                            <i class="fas fa-dollar-sign"></i>
                                        @elseif($anomalie['type'] === 'stock_discrepancy')
                                            <i class="fas fa-warehouse"></i>
                                        @elseif($anomalie['type'] === 'vente_annulation')
                                            <i class="fas fa-ban"></i>
                                        @elseif($anomalie['type'] === 'caisse_discrepancy')
                                            <i class="fas fa-cash-register"></i>
                                        @elseif($anomalie['type'] === 'login_failure')
                                            <i class="fas fa-user-lock"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}
                                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full 
                                        {{ $anomalie['status'] === 'nouvelle' ? 'bg-blue-100 text-blue-800' : 
                                          ($anomalie['status'] === 'en_cours' ? 'bg-yellow-100 text-yellow-800' : 
                                          ($anomalie['status'] === 'resolue' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($anomalie['status']) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $anomalie['message'] }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Détecté le {{ \Carbon\Carbon::parse($anomalie['created_at'])->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('audit.detail-anomalie', ['id' => $anomalie['id']]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Détails
                                </a>
                                @if($anomalie['status'] !== 'resolue' && $anomalie['status'] !== 'ignoree')
                                <form action="{{ route('audit.marquer-resolue', ['id' => $anomalie['id']]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        Marquer comme résolue
                                    </button>
                                </form>
                                <form action="{{ route('audit.ignorer-anomalie', ['id' => $anomalie['id']]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                        Ignorer
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $anomalies->links() }}
            </div>
            @else
            <div class="px-4 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune anomalie détectée</h3>
                <p class="mt-1 text-sm text-gray-500">Toutes les opérations semblent normales pour la période sélectionnée.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
