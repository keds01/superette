@extends('layouts.app')

@section('title', 'Tableau de bord d\'Audit')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Tableau de bord d'Audit</h1>
                <p class="mt-2 text-lg text-gray-500">Vue d'ensemble des activités et anomalies du système</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('audit.journal') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-gray-400 to-indigo-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-list"></i> Journal
                </a>
                <a href="{{ route('audit.anomalies') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-red-500 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-exclamation-triangle"></i> Anomalies
                </a>
                <a href="{{ route('audit.rapport-quotidien') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-chart-bar"></i> Rapport quotidien
                </a>
            </div>
        </div>

        <!-- Statistiques clés -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-indigo-100 rounded-full">
                        <i class="fas fa-clipboard-list text-indigo-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm text-indigo-700 font-medium">Total des activités</h3>
                        <p class="text-3xl font-extrabold text-indigo-900 mt-2">{{ $totalActivites }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-tr from-red-50 to-red-100 border border-red-200 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm text-red-700 font-medium">Anomalies détectées</h3>
                        <p class="text-3xl font-extrabold text-red-900 mt-2">{{ count($anomalies) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-full">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm text-green-700 font-medium">Ventes aujourd'hui</h3>
                        <p class="text-3xl font-extrabold text-green-900 mt-2">{{ $rapportQuotidien['ventes']['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm text-yellow-700 font-medium">CA du jour</h3>
                        <p class="text-3xl font-extrabold text-yellow-900 mt-2">{{ number_format($rapportQuotidien['ventes']['montant_total'] ?? 0, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques supplémentaires -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 mr-4">
                        <i class="fas fa-boxes text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-indigo-700">Total produits</h3>
                        <p class="text-2xl font-semibold text-indigo-900">{{ $statsSupplementaires['total_produits'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur-xl border border-orange-100 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 mr-4">
                        <i class="fas fa-exclamation-circle text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-orange-700">En alerte</h3>
                        <p class="text-2xl font-semibold text-orange-900">{{ $statsSupplementaires['produits_en_alerte'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 mr-4">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-red-700">En rupture</h3>
                        <p class="text-2xl font-semibold text-red-900">{{ $statsSupplementaires['produits_en_rupture'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <i class="fas fa-exchange-alt text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-purple-700">Mouvements stock</h3>
                        <p class="text-2xl font-semibold text-purple-900">{{ $statsSupplementaires['mouvements_stock'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableaux détaillés -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Dernières activités -->
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50/80">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-indigo-800">Dernières activités</h3>
                        <a href="{{ route('audit.journal') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-100 text-indigo-700 text-xs font-semibold shadow hover:bg-indigo-200 transition">
                            <i class="fas fa-list"></i> Voir tout
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-indigo-100">
                        <thead class="bg-indigo-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-indigo-50">
                            @forelse($dernieresActivites as $activite)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $activite->type == 'connexion' ? 'bg-green-100 text-green-800' : 
                                           ($activite->type == 'modification' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($activite->type == 'creation' ? 'bg-blue-100 text-blue-800' : 
                                           ($activite->type == 'suppression' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst($activite->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activite->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activite->user ? $activite->user->name : 'Système' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-info-circle text-gray-400 mb-2"></i>
                                    <p>Aucune activité récente</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Anomalies récentes -->
            <div class="bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50/80">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-red-800">Anomalies détectées</h3>
                        <a href="{{ route('audit.anomalies') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-red-100 text-red-700 text-xs font-semibold shadow hover:bg-red-200 transition">
                            <i class="fas fa-bug"></i> Voir tout
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-red-100">
                        <thead class="bg-red-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-red-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-red-700 uppercase tracking-wider">Sévérité</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-red-700 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-red-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-red-50">
                            @forelse($anomalies as $anomalie)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                    {{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ ($anomalie['severite'] ?? 'basse') === 'haute' ? 'bg-red-100 text-red-800' : 
                                           (($anomalie['severite'] ?? 'basse') === 'moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($anomalie['severite'] ?? 'basse') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($anomalie['message'], 50) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('audit.detail-anomalie', ['id' => $anomalie['id']]) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center py-4">
                                        <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                        <p>Aucune anomalie détectée</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rapport quotidien -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-indigo-800">Rapport quotidien - {{ $rapportQuotidien['date'] ?? now()->format('d/m/Y') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('audit.rapport-quotidien') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-100 text-indigo-700 text-xs font-semibold shadow hover:bg-indigo-200 transition">
                            <i class="fas fa-chart-bar"></i> Voir le détail
                        </a>
                        <a href="{{ route('audit.rapport-quotidien', ['format' => 'pdf']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-red-100 text-red-700 text-xs font-semibold shadow hover:bg-red-200 transition">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-xl shadow p-6 flex flex-col items-center">
                        <div class="text-sm text-indigo-700 font-medium">Ventes</div>
                        <div class="text-3xl font-extrabold text-indigo-900 mt-2">{{ $rapportQuotidien['ventes']['total'] ?? 0 }}</div>
                        <div class="text-sm text-indigo-600 mt-1">{{ number_format($rapportQuotidien['ventes']['montant_total'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="bg-gradient-to-tr from-purple-50 to-purple-100 border border-purple-200 rounded-xl shadow p-6 flex flex-col items-center">
                        <div class="text-sm text-purple-700 font-medium">Paiements</div>
                        <div class="text-3xl font-extrabold text-purple-900 mt-2">{{ $rapportQuotidien['paiements']['total'] ?? 0 }}</div>
                        <div class="text-sm text-purple-600 mt-1">{{ count($rapportQuotidien['paiements']['par_methode'] ?? []) }} méthodes</div>
                    </div>
                    <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-xl shadow p-6 flex flex-col items-center">
                        <div class="text-sm text-green-700 font-medium">Activités</div>
                        <div class="text-3xl font-extrabold text-green-900 mt-2">{{ $rapportQuotidien['activites']['total'] ?? 0 }}</div>
                        <div class="text-sm text-green-600 mt-1">Actions enregistrées</div>
                    </div>
                    <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl shadow p-6 flex flex-col items-center">
                        <div class="text-sm text-yellow-700 font-medium">Panier moyen</div>
                        <div class="text-3xl font-extrabold text-yellow-900 mt-2">{{ number_format($rapportQuotidien['ventes']['panier_moyen'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="text-sm text-yellow-600 mt-1">FCFA par vente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection