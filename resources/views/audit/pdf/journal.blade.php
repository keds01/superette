<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Journal des activités</title>
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
        .badge-blue {
            background-color: #e6f2ff;
            color: #0066cc;
        }
        .badge-green {
            background-color: #e6ffe6;
            color: #009900;
        }
        .badge-red {
            background-color: #ffe6e6;
            color: #cc0000;
        }
        .badge-gray {
            background-color: #f2f2f2;
            color: #666666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Journal des activités</h1>
        <p>Généré le {{ $date_generation->format('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="info-block">
        <h2 style="margin-top: 0; font-size: 14px;">Filtres appliqués</h2>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 25%;"><strong>Type d'activité:</strong></td>
                <td style="border: none;">{{ $filtres['type'] ?? 'Tous' }}</td>
                <td style="border: none; width: 25%;"><strong>Utilisateur:</strong></td>
                <td style="border: none;">{{ isset($filtres['user_id']) ? App\Models\User::find($filtres['user_id'])->name : 'Tous' }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Date début:</strong></td>
                <td style="border: none;">{{ $filtres['date_debut'] ?? 'Non spécifiée' }}</td>
                <td style="border: none;"><strong>Date fin:</strong></td>
                <td style="border: none;">{{ $filtres['date_fin'] ?? 'Non spécifiée' }}</td>
            </tr>
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 15%;">Utilisateur</th>
                <th style="width: 20%;">Détails</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activites as $activite)
            <tr>
                <td>{{ $activite->created_at->format('d/m/Y H:i:s') }}</td>
                <td>
                    @if(in_array($activite->type, ['connexion', 'consultation']))
                        <span class="badge badge-blue">{{ $activite->type }}</span>
                    @elseif(in_array($activite->type, ['creation', 'modification']))
                        <span class="badge badge-green">{{ $activite->type }}</span>
                    @elseif(in_array($activite->type, ['suppression', 'annulation']))
                        <span class="badge badge-red">{{ $activite->type }}</span>
                    @else
                        <span class="badge badge-gray">{{ $activite->type }}</span>
                    @endif
                </td>
                <td>{{ $activite->description }}</td>
                <td>{{ optional($activite->user)->name ?? 'Système' }}</td>
                <td>{{ $activite->model_type ? $activite->model_type . ' #' . $activite->model_id : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Aucune activité trouvée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système d'audit de la superette.</p>
        <p>Total des activités: {{ count($activites) }}</p>
    </div>
</body>
</html>
