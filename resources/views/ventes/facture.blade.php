<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $vente->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 200px;
        }
        .info-entreprise {
            text-align: right;
        }
        .titre {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #2c3e50;
        }
        .numero-facture {
            text-align: right;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .client-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .client-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            width: 300px;
            margin-left: auto;
        }
        .total-section table {
            margin-bottom: 0;
        }
        .total-section td {
            padding: 5px 10px;
        }
        .total-section tr:last-child td {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        .statut {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .statut-en-cours {
            background-color: #ffeeba;
            color: #856404;
        }
        .statut-terminee {
            background-color: #d4edda;
            color: #155724;
        }
        .statut-annulee {
            background-color: #f8d7da;
            color: #721c24;
        }
        .paiements {
            margin-top: 30px;
        }
        .paiements h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .paiement-item {
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .paiement-item:last-child {
            border-bottom: none;
        }
        .mentions {
            margin-top: 30px;
            font-size: 11px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .page {
                padding: 15mm;
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Imprimer la facture
        </button>
    </div>

    <div class="page">
        <div class="header">
            <div class="logo" style="display: flex; align-items: center;">
                <img src="/images/LOGO_ELIFRANC_PRIX.png" alt="Logo Elifranc" style="height: 128px; width: auto; display: block;">
            </div>
            <div class="info-entreprise">
                <p style="margin: 0;">{{ config('app.adresse') }}</p>
                <p style="margin: 5px 0;">Tél: {{ config('app.telephone') }}</p>
                <p style="margin: 0;">Email: {{ config('app.email') }}</p>
                @if(config('app.siret'))
                    <p style="margin: 5px 0;">SIRET: {{ config('app.siret') }}</p>
                @endif
            </div>
        </div>

        

        <div class="titre">FACTURE</div>

        <div class="numero-facture">
            <strong>N° Vente : {{ $vente->numero_vente ?? $vente->numero ?? $vente->id }}</strong><br>
            Date: {{ $vente->date_vente->format('d/m/Y H:i') }}<br>
            <span class="statut statut-{{ $vente->statut }}">
                {{ ucfirst($vente->statut) }}
            </span>
        </div>

        <div class="client-info">
            <h3>Client</h3>
            <p>
                <strong>{{ $vente->client->nom }} {{ $vente->client->prenom }}</strong><br>
                @if($vente->client->adresse)
                    {{ $vente->client->adresse }}<br>
                @endif
                @if($vente->client->code_postal || $vente->client->ville)
                    {{ $vente->client->code_postal }} {{ $vente->client->ville }}<br>
                @endif
                Tél: {{ $vente->client->telephone }}<br>
                @if($vente->client->email)
                    Email: {{ $vente->client->email }}<br>
                @endif
                <span style="font-weight:bold; color:#2c3e50;">Vendeur :</span> {{ $vente->employe ? $vente->employe->nom . ' ' . $vente->employe->prenom : 'Non spécifié' }}
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Prix unitaire HT</th>
                    <th class="text-right">Promo</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Remise</th>
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
                        $prixPromo = $detail->produit->prix_promo ?? $prixUnitaire;
                        $estEnPromo = $prixPromo < $prixUnitaire;
                        $sousTotal = ($estEnPromo ? $prixPromo : $prixUnitaire) * $detail->quantite;
                        if ($estEnPromo) {
                            $totalPromos += ($prixUnitaire - $prixPromo) * $detail->quantite;
                        }
                        $totalFinal += $sousTotal;
                    @endphp
                    <tr @if($estEnPromo) style="background:#fffbe6;" @endif>
                        <td>
                            {{ $detail->produit->nom }}
                            @if($estEnPromo)
                                <span style="background:#ffe066;color:#b8860b;font-size:10px;padding:2px 6px;border-radius:8px;margin-left:4px;">Promo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <span @if($estEnPromo) style="text-decoration:line-through;color:#bbb;" @endif>
                                {{ number_format($prixUnitaire, 2, ',', ' ') }} FCFA
                            </span>
                            @if($estEnPromo)
                                <br><span style="color:#27ae60;font-weight:bold;">{{ number_format($prixPromo, 2, ',', ' ') }} FCFA</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($estEnPromo)
                                <span style="color:#27ae60;font-weight:bold;">-{{ number_format($prixUnitaire - $prixPromo, 2, ',', ' ') }} FCFA</span>
                            @else
                                <span style="color:#bbb;">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->quantite }}</td>
                        <td class="text-right">{{ number_format($detail->remise, 2, ',', ' ') }} FCFA</td>
                        <td class="text-right">{{ number_format($sousTotal, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                @if($totalPromos > 0)
                <tr>
                    <td style="color:#27ae60;font-weight:bold;">Économies promotionnelles :</td>
                    <td class="text-right" style="color:#27ae60;font-weight:bold;">-{{ number_format($totalPromos, 2, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                <tr>
                    <td>Total TTC</td>
                    <td class="text-right">{{ number_format($totalFinal, 2, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td style="color:#27ae60;font-weight:bold;">Montant payé</td>
                    <td class="text-right" style="color:#27ae60;font-weight:bold;">{{ number_format($vente->montant_paye, 2, ',', ' ') }} FCFA</td>
                </tr>
                @php $reste = $vente->montant_total - $vente->montant_paye; @endphp
                @if($reste > 0)
                <tr>
                    <td style="color:#e74c3c;font-weight:bold;">Reste à payer</td>
                    <td class="text-right" style="color:#e74c3c;font-weight:bold;">{{ number_format($reste, 2, ',', ' ') }} FCFA</td>
                </tr>
                @elseif($reste < 0)
                <tr>
                    <td style="color:#27ae60;font-weight:bold;">Monnaie rendue</td>
                    <td class="text-right" style="color:#27ae60;font-weight:bold;">{{ number_format(abs($reste), 2, ',', ' ') }} FCFA</td>
                </tr>
                @else
                <tr>
                    <td style="color:#27ae60;font-weight:bold;">Payé intégralement</td>
                    <td class="text-right" style="color:#27ae60;font-weight:bold;">0 FCFA</td>
                </tr>
                @endif
            </table>
        </div>

        @if($vente->paiements->isNotEmpty())
            <div class="paiements">
                <h3>Paiements</h3>
                @foreach($vente->paiements as $paiement)
                    <div class="paiement-item">
                        <strong>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</strong> -
                        {{ ucfirst($paiement->mode_paiement) }} :
                        {{ number_format($paiement->montant, 2, ',', ' ') }} FCFA
                        @if($paiement->reference)
                            (Réf: {{ $paiement->reference }})
                        @endif
                        <span class="statut statut-{{ $paiement->statut }}">
                            {{ ucfirst($paiement->statut) }}
                        </span>
                    </div>
                @endforeach
                <div style="margin-top: 10px;">
                    <strong>Montant payé :</strong> {{ number_format($vente->montant_paye, 2, ',', ' ') }} FCFA<br>
                    @php
                        $reste = $vente->montant_total - $vente->montant_paye;
                    @endphp
                    @if($reste > 0)
                        <strong>Reste à payer :</strong> {{ number_format($reste, 2, ',', ' ') }} FCFA
                    @elseif($reste < 0)
                        <strong>Monnaie rendue :</strong> {{ number_format(abs($reste), 2, ',', ' ') }} FCFA
                    @else
                        <strong>Payé intégralement</strong>
                    @endif
                </div>
            </div>
        @endif

        @if($vente->notes)
            <div class="mentions">
                <strong>Notes :</strong><br>
                {{ $vente->notes }}
            </div>
        @endif

        <div class="footer">
            <p>Facture générée le {{ now()->format('d/m/Y H:i') }} par {{ $vente->employe->nom }} {{ $vente->employe->prenom }}</p>
            <p>Type de vente : {{ ucfirst($vente->type_vente) }}</p>
            <p>Merci de votre confiance !</p>
        </div>
    </div>
</body>
</html> 