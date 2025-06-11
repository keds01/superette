<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de caisse #{{ $vente->numero }}</title>
    <style>
        @media print {
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.4;
                margin: 0;
                padding: 10px;
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
        .ticket {
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 5px 0;
        }
        .info {
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        .items {
            margin: 10px 0;
        }
        .item {
            margin: 5px 0;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .totals {
            margin: 10px 0;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
        }
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #4a5568;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>SUPERETTE</h1>
            <p>123 Rue du Commerce</p>
            <p>Tél: +123 456 789</p>
        </div>

        <div class="info">
            <p>Ticket #{{ $vente->numero }}</p>
            <p>Date: {{ $vente->created_at->format('d/m/Y H:i') }}</p>
            <p>Caissier: {{ $vente->user->name }}</p>
        </div>

        <div class="items">
            <div class="item-header">
                <span>Article</span>
                <span>Total</span>
            </div>
            @foreach($vente->details as $detail)
            <div class="item">
                <div class="item-header">
                    <span>{{ $detail->product->nom }}</span>
                    <span>{{ number_format($detail->montant_total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="item-details">
                    <span>{{ $detail->quantite }} × {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                    @if($detail->remise > 0)
                    <span>-{{ number_format($detail->remise, 0, ',', ' ') }} FCFA</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-line">
                <span>Sous-total</span>
                <span>{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($vente->montant_remise > 0)
            <div class="total-line">
                <span>Remise</span>
                <span>-{{ number_format($vente->montant_remise, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="total-line" style="font-weight: bold;">
                <span>Total</span>
                <span>{{ number_format($vente->montant_total - $vente->montant_remise, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-line">
                <span>Mode de paiement</span>
                <span>{{ ucfirst($vente->mode_paiement) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Merci de votre visite !</p>
            <p>À bientôt</p>
        </div>
    </div>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn">Imprimer</button>
        <a href="{{ route('caisse') }}" class="btn">Nouvelle vente</a>
    </div>

    <script>
        // Impression automatique
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html> 