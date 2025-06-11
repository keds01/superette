@extends('layouts.app')

@section('title', 'Reçu de vente')

@section('content')
<div class="max-w-2xl mx-auto my-10 bg-white p-8 rounded-xl shadow-lg print:shadow-none print:p-0 print:bg-white">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-indigo-700">Reçu de vente</h2>
            <p class="text-sm text-gray-500">N° {{ $vente->id ?? '---' }} | {{ $vente->date_vente->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-400">{{ config('app.name') }}</span>
            <br>
            <span class="text-xs text-gray-400">{{ config('app.adresse', 'Adresse de la boutique') }}</span>
        </div>
    </div>
    <hr class="mb-4">
    <div class="mb-4">
        <h3 class="font-semibold text-indigo-600 mb-1">Client</h3>
        <div class="text-sm text-gray-700">
            {{ $vente->client->nom ?? 'Client inconnu' }} {{ $vente->client->prenom ?? '' }}<br>
            Tél : {{ $vente->client->telephone ?? '-' }}<br>
            Email : {{ $vente->client->email ?? '-' }}
        </div>
    </div>
    <div class="mb-6">
        <h3 class="font-semibold text-indigo-600 mb-1">Détails de la vente</h3>
        <table class="w-full text-sm border">
            <thead>
                <tr class="bg-indigo-50">
                    <th class="p-2 border">Produit</th>
                    <th class="p-2 border">Qté</th>
                    <th class="p-2 border">PU</th>
                    <th class="p-2 border">Remise</th>
                    <th class="p-2 border">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->lignes as $ligne)
                <tr>
                    <td class="p-2 border">{{ $ligne->produit->nom ?? '-' }}</td>
                    <td class="p-2 border text-center">{{ $ligne->quantite }}</td>
                    <td class="p-2 border text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="p-2 border text-right">{{ $ligne->remise_pourcentage ?? 0 }}%</td>
                    <td class="p-2 border text-right">{{ number_format($ligne->montant_total, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-4 flex justify-end">
        <table class="text-sm">
            <tr>
                <td class="pr-4 text-gray-600">Sous-total HT :</td>
                <td class="text-right font-medium">{{ number_format($vente->sous_total_ht, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="pr-4 text-gray-600">TVA :</td>
                <td class="text-right font-medium">{{ number_format($vente->montant_tva, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="pr-4 text-gray-600">Remises :</td>
                <td class="text-right font-medium">-{{ number_format($vente->total_remises, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="border-t">
                <td class="pr-4 text-indigo-700 font-bold">Total TTC :</td>
                <td class="text-right text-indigo-700 font-bold text-lg">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>
    <div class="flex justify-between items-center mt-8">
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 print:hidden">
            <i class="fas fa-print mr-2"></i> Imprimer
        </button>
        <span class="text-xs text-gray-400">Merci pour votre achat !</span>
    </div>
</div>
@endsection 