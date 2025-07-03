@extends('layouts.app')

@section('content')
<div class="py-8 max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-blue-800">Détail de la réception #{{ $reception->numero ?? $reception->id }}</h1>
        <a href="{{ route('receptions.index') }}" class="btn btn-secondary">Retour à la liste</a>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-gray-600">Fournisseur :</div>
                <div class="font-semibold">{{ $reception->fournisseur->nom ?? '-' }}</div>
            </div>
            <div>
                <div class="text-gray-600">Date réception :</div>
                <div class="font-semibold">{{ $reception->date_reception ? $reception->date_reception->format('d/m/Y') : '-' }}</div>
            </div>
            <div>
                <div class="text-gray-600">Numéro facture :</div>
                <div class="font-semibold">{{ $reception->numero_facture ?? '-' }}</div>
            </div>
            <div>
                <div class="text-gray-600">Utilisateur :</div>
                <div class="font-semibold">{{ $reception->user->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-gray-600">Statut :</div>
                <div class="font-semibold">{{ ucfirst($reception->statut) }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-blue-700">Produits réceptionnés</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-blue-100">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-blue-700 uppercase">Produit</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-blue-700 uppercase">Quantité</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-blue-700 uppercase">Prix unitaire</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-blue-700 uppercase">Date péremption</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-blue-700 uppercase">Sous-total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-50">
                    @foreach($reception->details as $detail)
                        <tr>
                            <td class="px-4 py-2">{{ $detail->produit->nom ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $detail->quantite }}</td>
                            <td class="px-4 py-2">{{ number_format($detail->prix_unitaire,2,',',' ') }} F</td>
                            <td class="px-4 py-2">{{ $detail->date_peremption ? $detail->date_peremption->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-2">{{ number_format($detail->quantite * $detail->prix_unitaire,2,',',' ') }} F</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
