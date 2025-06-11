<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket d'opération #{{ $operation->numero_operation }}</title>
    <style>
        @page {
            size: 80mm 297mm;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            margin: 0;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .info {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SUPERETTE</div>
        <div class="subtitle">Ticket d'opération</div>
        <div class="info">N° {{ $operation->numero_operation }}</div>
        <div class="info">{{ $operation->created_at->format('d/m/Y H:i') }}</div>
    </div>

    <div class="info">
        <span class="label">Type:</span>
        {{ $operation->type_operation === 'entree' ? 'ENTRÉE' : 'SORTIE' }}
    </div>

    <div class="amount {{ $operation->type_operation === 'entree' ? 'text-green-600' : 'text-red-600' }}">
        {{ $operation->type_operation === 'entree' ? '+' : '-' }}
        {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
    </div>

    <div class="divider"></div>

    <div class="info">
        <span class="label">Mode de paiement:</span>
        {{ ucfirst($operation->mode_paiement) }}
    </div>

    @if($operation->description)
    <div class="info">
        <span class="label">Description:</span>
        {{ $operation->description }}
    </div>
    @endif

    @if($operation->vente)
    <div class="divider"></div>
    <div class="info">
        <span class="label">Vente associée:</span>
        {{ $operation->vente->numero_vente }}
    </div>
    @endif

    <div class="divider"></div>

    <div class="info">
        <span class="label">Opérateur:</span>
        {{ $operation->user->name }}
    </div>

    <div class="footer">
        <div>Merci de votre confiance</div>
        <div>Ce ticket fait foi de transaction</div>
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()" style="padding: 5px 10px; background: #4F46E5; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Imprimer le ticket
            </button>
        </div>
    </div>

    <script>
        // Impression automatique
        window.onload = function() {
            if (!window.location.search.includes('no-print')) {
                window.print();
            }
        };
    </script>
</body>
</html> 