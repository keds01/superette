<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export des Produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2d3748;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2d3748;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stock-low {
            color: #e53e3e;
            font-weight: bold;
        }
        .stock-ok {
            color: #38a169;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #2d3748;
        }
        .summary p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventaire des Produits</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Total des produits : {{ $products->count() }}</p>
    </div>

    <div class="summary">
        <h3>Résumé</h3>
        @php
            $totalStock = $products->sum('stock');
            $totalValue = $products->sum(function($product) {
                return $product->stock * $product->prix_achat_ht;
            });
            $lowStockCount = $products->where('stock', '<=', 'seuil_alerte')->count();
        @endphp
        <p><strong>Valeur totale du stock :</strong> {{ number_format($totalValue, 0, ',', ' ') }} FCFA</p>
        <p><strong>Produits en stock bas :</strong> {{ $lowStockCount }}</p>
        <p><strong>Quantité totale en stock :</strong> {{ number_format($totalStock, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Référence</th>
                <th>Catégorie</th>
                <th>Stock</th>
                <th>Seuil d'alerte</th>
                <th>Prix d'achat HT</th>
                <th>Prix de vente TTC</th>
                <th>Péremption</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->nom }}</td>
                <td>{{ $product->reference }}</td>
                <td>{{ $product->categorie->nom ?? 'N/A' }}</td>
                <td class="{{ $product->stock <= $product->seuil_alerte ? 'stock-low' : 'stock-ok' }}">
                    {{ number_format($product->stock, 2) }} {{ $product->uniteVente->nom_court ?? '' }}
                </td>
                <td>{{ number_format($product->seuil_alerte, 2) }}</td>
                <td>{{ number_format($product->prix_achat_ht, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($product->prix_vente_ttc, 0, ',', ' ') }} FCFA</td>
                <td>
                    @if($product->date_peremption)
                        {{ $product->date_peremption->format('d/m/Y') }}
                        @if($product->date_peremption->isPast())
                            <span style="color: #e53e3e;">(Expiré)</span>
                        @elseif($product->date_peremption->diffInDays(now()) <= 15)
                            <span style="color: #d69e2e;">(Proche)</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion de superette</p>
        <p>Page 1</p>
    </div>
</body>
</html> 