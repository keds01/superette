<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport quotidien d'audit</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        h2 {
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .stat-container {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        .stat-box {
            flex: 1 0 45%;
            margin: 10px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 10px;
        }
        .badge-green {
            background-color: #e6ffe6;
            color: #009900;
        }
        .badge-red {
            background-color: #ffe6e6;
            color: #cc0000;
        }
        .page-break {
            page-break-after: always;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-green {
            color: #009900;
        }
        .text-red {
            color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport quotidien d'audit</h1>
        <p>{{ $date->format('d/m/Y') }}</p>
        <p>Généré le {{ $date_generation->format('d/m/Y à H:i') }}</p>
    </div>
    
    <!-- Résumé des ventes -->
    <div class="section">
        <h2>Résumé des ventes</h2>
        <div class="stat-container">
            <div class="stat-box">
                <div class="stat-label">Nombre de ventes</div>
                <div class="stat-value">{{ $rapportQuotidien['ventes']['total'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Chiffre d'affaires</div>
                <div class="stat-value" style="color: #009900;">{{ number_format($rapportQuotidien['ventes']['montant_total'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Panier moyen</div>
                <div class="stat-value">{{ number_format($rapportQuotidien['ventes']['panier_moyen'], 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Ventes annulées</div>
                <div class="stat-value" style="color: #cc0000;">{{ $rapportQuotidien['ventes']['annulees']['total'] }}</div>
            </div>
        </div>
        
        <!-- Ventes par heure (représentation textuelle pour PDF) -->
        <h3 style="font-size: 14px; margin-top: 15px;">Répartition des ventes par heure</h3>
        <table>
            <thead>
                <tr>
                    <th>Heure</th>
                    <th>Nombre de ventes</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapportQuotidien['ventes']['par_heure'] as $heure => $data)
                <tr>
                    <td>{{ $heure }}h</td>
                    <td>{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['montant'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Méthodes de paiement -->
    <div class="section">
        <h2>Méthodes de paiement</h2>
        <table>
            <thead>
                <tr>
                    <th>Méthode</th>
                    <th>Transactions</th>
                    <th>Montant</th>
                    <th>% du CA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapportQuotidien['paiements']['par_methode'] as $methode => $data)
                <tr>
                    <td>{{ ucfirst($methode) }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format(($data['total'] / $rapportQuotidien['paiements']['total_montant']) * 100, 1, ',', ' ') }}%</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td>{{ $rapportQuotidien['paiements']['total_transactions'] }}</td>
                    <td class="text-right">{{ number_format($rapportQuotidien['paiements']['total_montant'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Top produits vendus -->
    <div class="section">
        <h2>Top produits vendus</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Montant</th>
                    <th>% du CA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapportQuotidien['produits']['top_vendus'] as $produit)
                <tr>
                    <td>{{ $produit['nom'] }}</td>
                    <td>{{ $produit['quantite'] }}</td>
                    <td class="text-right">{{ number_format($produit['montant'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format(($produit['montant'] / $rapportQuotidien['ventes']['montant_total']) * 100, 1, ',', ' ') }}%</td>
                </tr>
                @endforeach
                @if(count($rapportQuotidien['produits']['top_vendus']) === 0)
                <tr>
                    <td colspan="4" class="text-center">Aucune vente enregistrée</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="page-break"></div>
    
    <!-- Activités -->
    <div class="section">
        <h2>Activités enregistrées</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapportQuotidien['activites']['par_type'] as $type => $count)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td>{{ $rapportQuotidien['activites']['total'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Anomalies -->
    <div class="section">
        <h2>Anomalies détectées</h2>
        @if(count($rapportQuotidien['anomalies']) > 0)
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Sévérité</th>
                    <th>Message</th>
                    <th>Heure</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapportQuotidien['anomalies'] as $anomalie)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}</td>
                    <td>
                        <span class="badge {{ $anomalie['severite'] === 'haute' ? 'badge-red' : 'badge-green' }}">
                            {{ ucfirst($anomalie['severite']) }}
                        </span>
                    </td>
                    <td>{{ $anomalie['message'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($anomalie['created_at'])->format('H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center">Aucune anomalie détectée pour cette journée</p>
        @endif
    </div>
    
    <!-- Résumé et recommandations -->
    <div class="section">
        <h2>Résumé et recommandations</h2>
        <p>Résumé de la journée du {{ $date->format('d/m/Y') }} :</p>
        <ul>
            <li>Chiffre d'affaires: <strong>{{ number_format($rapportQuotidien['ventes']['montant_total'], 0, ',', ' ') }} FCFA</strong> 
                ({{ $rapportQuotidien['ventes']['montant_total'] > $rapportQuotidien['objectif_journalier'] ? 'Au-dessus' : 'En-dessous' }} de l'objectif 
                de {{ number_format($rapportQuotidien['objectif_journalier'], 0, ',', ' ') }} FCFA)</li>
            <li>{{ $rapportQuotidien['ventes']['total'] }} ventes enregistrées avec un panier moyen de 
                {{ number_format($rapportQuotidien['ventes']['panier_moyen'], 0, ',', ' ') }} FCFA</li>
            <li>{{ $rapportQuotidien['ventes']['annulees']['total'] }} ventes annulées pour un montant de 
                {{ number_format($rapportQuotidien['ventes']['annulees']['montant_total'], 0, ',', ' ') }} FCFA</li>
            <li>{{ count($rapportQuotidien['anomalies']) }} anomalies détectées</li>
        </ul>
        
        @if(count($rapportQuotidien['recommandations']) > 0)
        <p>Recommandations :</p>
        <ul>
            @foreach($rapportQuotidien['recommandations'] as $recommandation)
            <li>{{ $recommandation }}</li>
            @endforeach
        </ul>
        @endif
    </div>
    
    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système d'audit de la superette.</p>
    </div>
</body>
</html>
