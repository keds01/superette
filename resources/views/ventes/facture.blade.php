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
            <div class="logo">
                @if(config('app.logo'))
                    <img src="{{ config('app.logo') }}" alt="Logo" style="max-width: 200px;">
                @else
                    <h1 style="margin: 0;">{{ config('app.name') }}</h1>
                @endif
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
            <strong>N° {{ $vente->numero }}</strong><br>
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
                    Email: {{ $vente->client->email }}
                @endif
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Prix unitaire HT</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Remise</th>
                    <th class="text-right">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->details as $detail)
                    <tr>
                        <td>{{ $detail->produit->nom }}</td>
                        <td class="text-right">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                        <td class="text-center">{{ $detail->quantite }}</td>
                        <td class="text-right">{{ number_format($detail->remise, 2, ',', ' ') }} FCFA</td>
                        <td class="text-right">{{ number_format($detail->montant_total, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                <tr>
                    <td>Total HT</td>
                    <td class="text-right">{{ number_format($vente->montant_ht, 2, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td>TVA (20%)</td>
                    <td class="text-right">{{ number_format($vente->montant_tva, 2, ',', ' ') }} FCFA</td>
                </tr>
                @if($vente->montant_remise > 0)
                    <tr>
                        <td>Remises</td>
                        <td class="text-right">-{{ number_format($vente->montant_remise, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @endif
                <tr>
                    <td>Total TTC</td>
                    <td class="text-right">{{ number_format($vente->montant_total, 2, ',', ' ') }} FCFA</td>
                </tr>
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
                    <strong>Reste à payer :</strong> {{ number_format($vente->montant_total - $vente->montant_paye, 2, ',', ' ') }} FCFA
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