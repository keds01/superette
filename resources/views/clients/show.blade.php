@extends('layouts.app')

@section('title', 'Détails du Client - ' . $client->nom . ' ' . $client->prenom)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-blue-400 to-indigo-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <i class="fas fa-user-tie text-indigo-500"></i>
                            Détails du Client
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ $client->nom }} {{ $client->prenom }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('clients.edit', $client->id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        <a href="{{ route('clients.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations principales du client -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-500"></i>
                            Informations Générales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nom</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->nom }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Prénom</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->prenom }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->telephone }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->email ?? '--' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Adresse</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->adresse ?? '--' }}</p>
                            </div>
                             <div>
                                <p class="text-sm font-medium text-gray-500">Type de client</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->type ? ucfirst($client->type) : '--' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Statut</p>
                                <p class="mt-1 text-lg">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $client->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($client->statut) }}
                                    </span>
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Notes</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->notes ?? '--' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date d'inscription</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des Ventes Récentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-history text-indigo-500"></i>
                            Dernières Ventes (5)
                        </h3>
                        @if($client->ventes && $client->ventes->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Net</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($client->ventes->sortByDesc('date_vente')->take(5) as $vente)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $vente->numero_vente }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ number_format($vente->montant_net, 0, ',', ' ') }} FCFA</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vente->statut_color['bg'] }} {{ $vente->statut_color['text'] }}">
                                                        {{ ucfirst($vente->statut) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                    <a href="{{ route('ventes.show', $vente->id) }}" class="text-indigo-600 hover:text-indigo-800" title="Voir la vente">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                             @if($client->ventes->count() > 5)
                                <div class="mt-4 text-right">
                                    <a href="{{ route('ventes.index', ['client_id' => $client->id]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Voir toutes les ventes ({{ $client->ventes->count() }}) <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500">Aucune vente enregistrée pour ce client.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistiques du Client -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-indigo-500"></i>
                            Statistiques Clés
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre total de ventes</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $ventes_count }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Montant total dépensé</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($ventes_sum_montant_net, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date de la dernière vente</p>
                                <p class="mt-1 text-lg text-gray-700">{{ $derniere_vente_date ? $derniere_vente_date->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            @if(isset($client->points_fidelite))
                            <div>
                                <p class="text-sm font-medium text-gray-500">Points de fidélité</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $client->points_fidelite ?? 0 }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-bolt text-indigo-500"></i>
                            Actions Rapides
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('ventes.create', ['client_id' => $client->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-emerald-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                                <i class="fas fa-plus-circle"></i>
                                Nouvelle Vente
                            </a>
                            {{-- <a href="#" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                                <i class="fas fa-comment-dots"></i>
                                Envoyer un Message
                            </a> --}}
                             <a href="{{ route('ventes.index', ['client_id' => $client->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-100 border border-gray-300 text-gray-700 font-semibold shadow-sm hover:bg-gray-200 transition-all duration-200">
                                <i class="fas fa-receipt"></i>
                                Voir toutes les ventes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
