@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-5">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                <svg class="mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Retour
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Détails de l'anomalie</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $anomalie['created_at']->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $anomalie['severite'] === 'haute' ? 'bg-red-100 text-red-800' : 
                    ($anomalie['severite'] === 'moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($anomalie['severite']) }}
                    </span>
                    <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $anomalie['status'] === 'nouvelle' ? 'bg-blue-100 text-blue-800' : 
                    ($anomalie['status'] === 'en_cours' ? 'bg-yellow-100 text-yellow-800' : 
                    ($anomalie['status'] === 'resolue' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($anomalie['status']) }}
                    </span>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Type d'anomalie</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $anomalie['message'] }}</dd>
                    </div>
                    
                    @if($anomalie['type'] === 'prix_variation')
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Produit</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $produit->nom }} ({{ $produit->reference }})
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Ancien prix</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['ancien_prix'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nouveau prix</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['nouveau_prix'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Variation</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['pourcentage_variation'], 2, ',', ' ') }}%
                        </dd>
                    </div>
                    @endif
                    
                    @if($anomalie['type'] === 'stock_discrepancy')
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Produit</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $produit->nom }} ({{ $produit->reference }})
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Stock système</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['stock_systeme'] }} unités
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Stock physique</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['stock_physique'] }} unités
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Écart</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['ecart'] }} unités ({{ number_format($anomalie['details']['pourcentage_ecart'], 2, ',', ' ') }}%)
                        </dd>
                    </div>
                    @endif
                    
                    @if($anomalie['type'] === 'vente_annulation')
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Vente</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            #{{ $anomalie['details']['vente_id'] }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Montant</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['montant'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Utilisateur</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $utilisateur ? $utilisateur->name : 'Inconnu' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Raison d'annulation</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['raison'] ?? 'Non spécifiée' }}
                        </dd>
                    </div>
                    @endif
                    
                    @if($anomalie['type'] === 'caisse_discrepancy')
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Caisse</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['caisse_id'] }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Solde système</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['solde_systeme'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Solde physique</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['solde_physique'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Écart</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ number_format($anomalie['details']['ecart'], 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    @endif
                    
                    @if($anomalie['type'] === 'login_failure')
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Utilisateur</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['email'] ?? 'Inconnu' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tentatives</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['tentatives'] }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Adresse IP</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['details']['ip_address'] }}
                        </dd>
                    </div>
                    @endif
                    
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Détecté par</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $anomalie['detected_by'] ?? 'Système d\'audit automatique' }}
                        </dd>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Activités liées</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if(count($activitesLiees) > 0)
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($activitesLiees as $activite)
                                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                        <span class="flex-1 w-0 truncate">
                                            {{ $activite->type }} - {{ $activite->description }}
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="text-gray-500">{{ $activite->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-sm text-gray-500">Aucune activité liée trouvée</p>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <!-- Actions -->
        @if($anomalie['status'] !== 'resolue' && $anomalie['status'] !== 'ignoree')
        <div class="mt-6 flex justify-end space-x-4">
            <form action="{{ route('audit.marquer-en-cours', ['id' => $anomalie['id']]) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-secondary">
                    Marquer en cours de traitement
                </button>
            </form>
            <form action="{{ route('audit.marquer-resolue', ['id' => $anomalie['id']]) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-primary">
                    Marquer comme résolue
                </button>
            </form>
            <form action="{{ route('audit.ignorer-anomalie', ['id' => $anomalie['id']]) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-danger">
                    Ignorer cette anomalie
                </button>
            </form>
        </div>
        @endif
        
        <!-- Ajouter un commentaire -->
        @if($anomalie['status'] !== 'ignoree')
        <div class="mt-6 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ajouter un commentaire</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Ajoutez un commentaire pour documenter les actions prises ou les observations.</p>
                </div>
                <form action="{{ route('audit.ajouter-commentaire', ['id' => $anomalie['id']]) }}" method="POST" class="mt-5">
                    @csrf
                    <div>
                        <textarea rows="3" name="commentaire" id="commentaire" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn-primary">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
            
            @if(count($commentaires) > 0)
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Commentaires précédents</h3>
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($commentaires as $commentaire)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                            <span class="text-xs font-medium text-white">{{ substr($commentaire['user']['name'] ?? 'SYS', 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">{{ $commentaire['user']['name'] ?? 'Système' }}</span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                {{ $commentaire['created_at']->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>{{ $commentaire['commentaire'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
