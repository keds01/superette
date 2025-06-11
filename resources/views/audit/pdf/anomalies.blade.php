<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport d'anomalies</title>
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
        .info-block {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f7f7f7;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 10px;
        }
        .badge-high {
            background-color: #ffe6e6;
            color: #cc0000;
        }
        .badge-medium {
            background-color: #fff2cc;
            color: #cc7a00;
        }
        .badge-low {
            background-color: #e6ffe6;
            color: #009900;
        }
        .badge-new {
            background-color: #e6f2ff;
            color: #0066cc;
        }
        .badge-inprogress {
            background-color: #fff2cc;
            color: #cc7a00;
        }
        .badge-resolved {
            background-color: #e6ffe6;
            color: #009900;
        }
        .badge-ignored {
            background-color: #f2f2f2;
            color: #666666;
        }
        .page-break {
            page-break-after: always;
        }
        .anomaly-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 10px;
        }
        .anomaly-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .anomaly-title {
            font-weight: bold;
            font-size: 13px;
        }
        .anomaly-date {
            font-size: 11px;
            color: #666;
        }
        .anomaly-description {
            margin-bottom: 8px;
        }
        .anomaly-footer {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'anomalies</h1>
        <p>Généré le {{ $date_generation->format('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="info-block">
        <h2 style="margin-top: 0; font-size: 14px;">Filtres appliqués</h2>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 25%;"><strong>Type d'anomalie:</strong></td>
                <td style="border: none;">{{ $filtres['type'] ?? 'Tous' }}</td>
                <td style="border: none; width: 25%;"><strong>Sévérité:</strong></td>
                <td style="border: none;">{{ $filtres['severite'] ?? 'Toutes' }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Statut:</strong></td>
                <td style="border: none;">{{ $filtres['status'] ?? 'Tous' }}</td>
                <td style="border: none;"><strong>Période:</strong></td>
                <td style="border: none;">{{ isset($filtres['date_debut']) ? 'Du '.$filtres['date_debut'] : '' }} {{ isset($filtres['date_fin']) ? 'au '.$filtres['date_fin'] : '' }}</td>
            </tr>
        </table>
    </div>
    
    <h2 style="font-size: 14px;">Liste des anomalies ({{ count($anomalies) }})</h2>
    
    @if(count($anomalies) > 0)
        @foreach($anomalies as $anomalie)
        <div class="anomaly-card">
            <div class="anomaly-header">
                <div class="anomaly-title">
                    {{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}
                    <span class="badge 
                        {{ $anomalie['severite'] === 'haute' ? 'badge-high' : 
                          ($anomalie['severite'] === 'moyenne' ? 'badge-medium' : 'badge-low') }}">
                        {{ ucfirst($anomalie['severite']) }}
                    </span>
                    <span class="badge 
                        {{ $anomalie['status'] === 'nouvelle' ? 'badge-new' : 
                          ($anomalie['status'] === 'en_cours' ? 'badge-inprogress' : 
                          ($anomalie['status'] === 'resolue' ? 'badge-resolved' : 'badge-ignored')) }}">
                        {{ ucfirst($anomalie['status']) }}
                    </span>
                </div>
                <div class="anomaly-date">
                    {{ \Carbon\Carbon::parse($anomalie['created_at'])->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <div class="anomaly-description">
                {{ $anomalie['message'] }}
            </div>
            
            <div class="anomaly-footer">
                <div>
                    @if(isset($anomalie['details']['produit_id']))
                    Produit #{{ $anomalie['details']['produit_id'] }}
                    @elseif(isset($anomalie['details']['user_id']))
                    Utilisateur #{{ $anomalie['details']['user_id'] }}
                    @elseif(isset($anomalie['details']['vente_id']))
                    Vente #{{ $anomalie['details']['vente_id'] }}
                    @endif
                </div>
                <div>
                    ID: {{ $anomalie['id'] }}
                </div>
            </div>
        </div>
        @endforeach
    @else
        <p style="text-align: center; color: #666;">Aucune anomalie ne correspond aux critères spécifiés.</p>
    @endif
    
    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système d'audit de la superette.</p>
    </div>
</body>
</html>
