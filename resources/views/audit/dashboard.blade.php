@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-tr from-purple-400 to-pink-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3 animate-fade-in">
                        <svg class="w-8 h-8 text-purple-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m3 14v-2m0 2a2 2 0 002-2m-7 0h-2a2 2 0 00-2 2" />
                        </svg>
                        Tableau de bord d'Audit
                    </h2>
                    <p class="mt-2 text-lg text-gray-500">Vue d'ensemble des activités et anomalies du système</p>
                </div>
                {{-- Add a button here if needed, similar to the main dashboard --}}
                {{-- <a href="{{ route('some.route') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouvel élément
                </a> --}}
            </div>

            <!-- Statistiques clés -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total des activités -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m3 14v-2m0 2a2 2 0 002-2m-7 0h-2a2 2 0 00-2 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Total des activités</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalActivites }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anomalies détectées -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Anomalies détectées</h3>
                                <p class="text-2xl font-bold text-red-600">{{ count($anomalies) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ventes aujourd'hui -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 9h6m-3 3h3m-3 3h3m-6 1a3 3 0 11-6 0 3 3 0 016 0zm-3 0a1 1 0 100-2 1 1 0 000 2zm13 1a3 3 0 11-6 0 3 3 0 016 0zm-3 0a1 1 0 100-2 1 1 0 000 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Ventes aujourd'hui</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ $rapportQuotidien['ventes']['total'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CA du jour -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">CA du jour</h3>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($rapportQuotidien['ventes']['montant_total'] ?? 0, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableaux détaillés -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Dernières activités -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Dernières activités</h3>
                            <a href="{{ route('audit.journal') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tout</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($dernieresActivites as $activite)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $activite->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activite->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activite->user ? $activite->user->name : 'Système' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune activité récente</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Anomalies récentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Anomalies détectées</h3>
                            <a href="{{ route('audit.anomalies') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Voir tout</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sévérité</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($anomalies as $anomalie)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">{{ $anomalie['type'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ ($anomalie['severite'] ?? 'basse') === 'haute' ? 'bg-red-100 text-red-800' : 
                                                   (($anomalie['severite'] ?? 'basse') === 'moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($anomalie['severite'] ?? 'basse') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $anomalie['message'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(isset($anomalie['details']['produit_id']))
                                            <a href="{{ route('audit.detail-anomalie', ['type' => $anomalie['type'], 'id' => $anomalie['details']['produit_id']]) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                            @elseif(isset($anomalie['details']['user_id']))
                                            <a href="{{ route('audit.detail-anomalie', ['type' => $anomalie['type'], 'id' => $anomalie['details']['user_id']]) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune anomalie détectée</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rapport quotidien -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Rapport quotidien</h3>
                        <div>
                            <a href="{{ route('audit.rapport-quotidien') }}" class="mr-2 text-sm text-indigo-600 hover:text-indigo-900">Voir le détail</a>
                            <a href="{{ route('audit.rapport-quotidien', ['format' => 'pdf']) }}" class="text-sm text-red-600 hover:text-red-900">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium text-gray-500">Ventes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $rapportQuotidien['ventes']['total'] ?? 0 }} ventes pour un total de 
                                    {{ number_format($rapportQuotidien['ventes']['montant_total'] ?? 0, 0, ',', ' ') }} FCFA
                                </dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium text-gray-500">Panier moyen</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ number_format($rapportQuotidien['ventes']['panier_moyen'] ?? 0, 0, ',', ' ') }} FCFA
                                </dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium text-gray-500">Ventes annulées</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $rapportQuotidien['ventes']['annulees']['total'] ?? 0 }} pour un montant de 
                                    {{ number_format($rapportQuotidien['ventes']['annulees']['montant_total'] ?? 0, 0, ',', ' ') }} FCFA
                                </dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium text-gray-500">Paiements</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div>
                                        @foreach($rapportQuotidien['paiements']['par_methode'] ?? [] as $methode => $data)
                                        <div class="mb-1">{{ ucfirst($methode) }}: {{ $data['count'] ?? 0 }} paiements pour 
                                            {{ number_format($data['total'] ?? 0, 0, ',', ' ') }} FCFA</div>
                                        @endforeach
                                    </div>
                                </dd>
                            </div>
                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium text-gray-500">Activités</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ $rapportQuotidien['activites']['total'] ?? 0 }} activités enregistrées
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
