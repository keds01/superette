@extends('layouts.app')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Détails de l'Opération</h2>
                <p class="mt-1 text-sm text-gray-500">
                    N° {{ $caisse->numero_operation }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                @if($caisse->vente_id)
                <a href="{{ route('caisse.imprimerRecuOperation', ['caisse' => $caisse->id]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition"
                   target="_blank">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimer le reçu
                </a>
                @endif
                <a href="{{ route('caisse.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations principales -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'opération</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type d'opération</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $caisse->type_operation === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $caisse->type_operation === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Montant</dt>
                        <dd class="mt-1 text-lg font-semibold 
                            {{ $caisse->type_operation === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $caisse->type_operation === 'entree' ? '+' : '-' }}
                            {{ number_format($caisse->montant, 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Mode de paiement</dt>
                        <dd class="mt-1 text-gray-900">{{ ucfirst($caisse->mode_paiement) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date et heure</dt>
                        <dd class="mt-1 text-gray-900">{{ $caisse->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $caisse->description ?: 'Aucune description' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Informations complémentaires -->
            <div class="space-y-6">
                <!-- Opérateur -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Opérateur</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold text-lg">
                                    {{ substr($caisse->user->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $caisse->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $caisse->user->email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Vente associée -->
                @if($caisse->vente)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vente associée</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">N° Vente</dt>
                            <dd class="mt-1 text-gray-900">{{ $caisse->vente->numero_vente }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Montant total</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ number_format($caisse->vente->montant_total, 0, ',', ' ') }} FCFA
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date</dt>
                            <dd class="mt-1 text-gray-900">
                                {{ $caisse->vente->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
                @endif

                <!-- Solde après opération -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Solde après opération</h3>
                    <p class="text-2xl font-bold text-indigo-600">
                        {{ number_format($caisse->solde, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 