@extends('layouts.app')

@section('content')
<div class="flex justify-center py-8 print:py-0">
    <div class="w-full max-w-md bg-white rounded shadow-md p-6 print:shadow-none print:border print:rounded-none print:p-2">
        <!-- Header -->
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold text-indigo-700 tracking-wide">Ticket de Vente</h2>
            <div class="text-xs text-gray-500">N° {{ $vente->numero_vente }}</div>
            <div class="text-xs text-gray-400">Le {{ date('d/m/Y H:i', strtotime($vente->date_vente)) }}</div>
        </div>
        <!-- Client & Vendeur -->
        <div class="flex justify-between text-xs mb-2">
            <div>Client : <span class="font-semibold text-gray-700">{{ $vente->client->nom }} {{ $vente->client->prenom }}</span></div>
            <div>Vendeur : <span class="font-semibold text-gray-700">{{ $vente->employe->nom }}</span></div>
        </div>
        <!-- Produits -->
        <div class="border-t border-b border-dashed border-gray-300 py-2 my-2">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-500">
                        <th class="text-left">Produit</th>
                        <th class="text-center">Qté</th>
                        <th class="text-right">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vente->details as $detail)
                    <tr>
                        <td class="py-1 text-gray-700">{{ $detail->produit->nom }}</td>
                        <td class="py-1 text-center">{{ $detail->quantite }}</td>
                        <td class="py-1 text-right">{{ number_format($detail->sous_total, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Totaux -->
        <div class="flex flex-col gap-1 text-sm mb-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Montant total</span>
                <span class="font-bold text-indigo-700">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Montant payé</span>
                <span class="font-bold text-green-600">{{ number_format($vente->montant_paye, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Reste à payer</span>
                <span class="font-bold @if($vente->montant_restant>0) text-red-600 @else text-green-600 @endif">{{ number_format($vente->montant_restant, 0, ',', ' ') }} F</span>
            </div>
        </div>
        <!-- Footer -->
        <div class="mt-4 text-center text-xs text-gray-400 border-t pt-2">
            Merci pour votre achat !<br>
            <span class="not-italic">Superette - {{ config('app.name') }}</span>
        </div>
        <div class="mt-2 text-center print:hidden">
            <button onclick="window.print()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
</div>
@endsection
