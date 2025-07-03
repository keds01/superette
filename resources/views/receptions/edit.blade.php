@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        @if ($errors->any())
            <div class="mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-pulse" role="alert">
                    <strong class="font-bold">Erreur{{ $errors->count() > 1 ? 's' : '' }} :</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-pulse" role="alert">
                    <strong class="font-bold">Erreur : </strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3 animate-fade-in">
                    <svg class="w-8 h-8 text-indigo-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Édition Réception
                </h2>
                <p class="mt-2 text-lg text-gray-500">Modifiez les informations de la réception et ses produits</p>
            </div>
            <a href="{{ route('receptions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        @if(session('error'))
            <div class="mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-pulse" role="alert">
                    <strong class="font-bold">Erreur : </strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('receptions.update', $reception) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-xl border border-indigo-200 p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10-5h2a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h2" />
                    </svg>
                    Informations de réception
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="fournisseur_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
        <select name="fournisseur_id" id="fournisseur_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach($fournisseurs as $fournisseur)
                <option value="{{ $fournisseur->id }}" @if($reception->fournisseur_id == $fournisseur->id) selected @endif>{{ $fournisseur->nom }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="date_reception" class="block text-sm font-medium text-gray-700 mb-1">Date de réception <span class="text-red-500">*</span></label>
        <input type="date" id="date_reception" name="date_reception" value="{{ $reception->date_reception ? $reception->date_reception->format('Y-m-d') : '' }}"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>
    <div>
        <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement <span class="text-red-500">*</span></label>
        <select id="mode_paiement" name="mode_paiement" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">-- Sélectionner --</option>
            <option value="especes" {{ (old('mode_paiement', $reception->mode_paiement) == 'especes') ? 'selected' : '' }}>Espèces</option>
            <option value="cheque" {{ (old('mode_paiement', $reception->mode_paiement) == 'cheque') ? 'selected' : '' }}>Chèque</option>
            <option value="virement" {{ (old('mode_paiement', $reception->mode_paiement) == 'virement') ? 'selected' : '' }}>Virement</option>
            <option value="autre" {{ (old('mode_paiement', $reception->mode_paiement) == 'autre') ? 'selected' : '' }}>Autre</option>
        </select>
    </div>
    <div class="col-span-2">
        <label for="numero_facture" class="block text-sm font-medium text-gray-700 mb-1">Numéro de facture</label>
        <input type="text" id="numero_facture" name="numero_facture" value="{{ $reception->numero_facture }}"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
        <select name="statut" id="statut" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="en_cours" @if($reception->statut=='en_cours') selected @endif>En cours</option>
            <option value="terminee" @if($reception->statut=='terminee') selected @endif>Terminée</option>
        </select>
    </div>
</div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Produits réceptionnés
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-indigo-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Produit</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Quantité</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Prix unitaire</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Date péremption</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reception->details as $detail)
                                <tr class="bg-white border-b">
                                    <td class="px-4 py-2">{{ $detail->produit->nom ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" name="details[{{ $detail->id }}][quantite]" value="{{ $detail->quantite }}" class="w-24 text-center rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" name="details[{{ $detail->id }}][prix_unitaire]" value="{{ $detail->prix_unitaire }}" class="w-28 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-right">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="date" name="details[{{ $detail->id }}][date_peremption]" value="{{ $detail->date_peremption ? $detail->date_peremption->format('Y-m-d') : '' }}" class="w-36 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('receptions.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
