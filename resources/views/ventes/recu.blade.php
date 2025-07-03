@php ob_start(); @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Vente</title>
    <link rel="icon" type="image/png" href="/images/LOGO_ELIFRANC_PRIX.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex justify-center py-8 print:py-0">
    <div class="w-full max-w-xs bg-white rounded shadow-md p-4 print:shadow-none print:border print:rounded-none print:p-2">
        <!-- Logo Elifranc -->
        <div class="flex flex-col items-center mb-2">
            <img src="/images/LOGO_ELIFRANC_PRIX.png" alt="Logo Elifranc" class="h-32 w-auto">
            <div class="mt-2 text-xs text-indigo-700 font-bold">
                Superette : {{ (activeSuperette() && activeSuperette()->nom) ? activeSuperette()->nom . (activeSuperette()->code ? ' (Code: ' . activeSuperette()->code . ')' : '') : 'Non spécifiée' }}
            </div>
        </div>
        <!-- Header -->
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold text-indigo-700 tracking-wide">Ticket de Vente</h2>
            <div class="text-xs text-gray-500">N° {{ $vente->numero_vente }}</div>
            <div class="text-xs text-gray-400">Le {{ date('d/m/Y H:i', strtotime($vente->date_vente)) }}</div>
        </div>
        <!-- Client & Vendeur -->
        <div class="flex justify-between text-xs mb-2">
            <div>Client : <span class="font-semibold text-gray-700">{{ $vente->client ? $vente->client->nom . ' ' . $vente->client->prenom : 'Non spécifié' }}</span></div>
            <div>Vendeur : <span class="font-semibold text-gray-700">{{ $vente->employe ? $vente->employe->nom : 'Non spécifié' }}</span></div>
        </div>
        <!-- Produits -->
        <div class="border-t border-b border-dashed border-gray-300 py-2 my-2">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-500">
                        <th class="text-left">Produit</th>
                        <th class="text-center">Qté</th>
                        <th class="text-right">Prix</th>
                        <th class="text-right">Promo</th>
                        <th class="text-right">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPromos = 0;
                        $totalFinal = 0;
                    @endphp
                    @foreach($vente->details as $detail)
                        @php
                            $prixUnitaire = $detail->prix_unitaire;
                            $prixPromo = $detail->produit ? ($detail->produit->prix_promo ?? $prixUnitaire) : $prixUnitaire;
                            $estEnPromo = $prixPromo < $prixUnitaire;
                            $sousTotal = ($estEnPromo ? $prixPromo : $prixUnitaire) * $detail->quantite;
                            if ($estEnPromo) {
                                $totalPromos += ($prixUnitaire - $prixPromo) * $detail->quantite;
                            }
                            $totalFinal += $sousTotal;
                        @endphp
                        <tr @if($estEnPromo) class="bg-yellow-50" @endif>
                            <td class="py-1 text-gray-700">
                                {{ $detail->produit ? $detail->produit->nom : 'Produit supprimé' }}
                                @if($estEnPromo)
                                    <span class="ml-1 px-1 bg-yellow-100 text-yellow-800 text-[10px] rounded">Promo</span>
                                @endif
                            </td>
                        <td class="py-1 text-center">{{ $detail->quantite }}</td>
                            <td class="py-1 text-right">
                                <span @if($estEnPromo) class="line-through text-gray-400" @endif>
                                    {{ number_format($prixUnitaire, 0, ',', ' ') }} F
                                </span>
                                @if($estEnPromo)
                                    <br><span class="text-green-600 font-bold">{{ number_format($prixPromo, 0, ',', ' ') }} F</span>
                                @endif
                            </td>
                            <td class="py-1 text-right">
                                @if($estEnPromo)
                                    <span class="text-green-600 font-bold">-{{ number_format($prixUnitaire - $prixPromo, 0, ',', ' ') }} F</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-1 text-right">{{ number_format($sousTotal, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Totaux -->
        <div class="flex flex-col gap-1 text-sm mb-2">
            @if($totalPromos > 0)
            <div class="flex justify-between">
                <span class="text-green-600 font-bold">Économies promo</span>
                <span class="text-green-600 font-bold">-{{ number_format($totalPromos, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-gray-600">Montant total</span>
                <span class="font-bold text-indigo-700">{{ number_format($totalFinal, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Montant payé</span>
                <span class="font-bold text-green-600">{{ number_format($vente->montant_paye, 0, ',', ' ') }} F</span>
            </div>
            @if($vente->montant_restant > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Reste à payer</span>
                    <span class="font-bold text-red-600">{{ number_format($vente->montant_restant, 0, ',', ' ') }} F</span>
                </div>
            @elseif($vente->montant_restant < 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Monnaie rendue</span>
                    <span class="font-bold text-green-600">{{ number_format(abs($vente->montant_restant), 0, ',', ' ') }} F</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span class="text-gray-600">Payé intégralement</span>
                    <span class="font-bold text-green-600">0 F</span>
                </div>
            @endif
        </div>
        <!-- Footer -->
        <div class="mt-4 text-center text-xs text-gray-400 border-t pt-2">
            Merci pour votre achat !
        </div>
        <div class="mt-2 text-center print:hidden">
            <button onclick="window.print()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
</div>
</body>
</html>
@php ob_end_flush(); @endphp
