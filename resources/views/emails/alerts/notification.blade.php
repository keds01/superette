<!DOCTYPE html>
<html>
<head>
    <title>Notification d'Alerte Système</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        p {
            margin-bottom: 10px;
        }
        .alert-details strong {
            display: inline-block;
            width: 150px;
            color: #555;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div style="text-align:center; margin-bottom: 24px;">
        <img src="{{ asset('images/LOGO_ELIFRANC_PRIX.png') }}" alt="Logo Elifranc" style="height:48px; width:auto;">
    </div>
    <div class="container">
        <h2>Notification d'Alerte Système</h2>

        <p>Bonjour,</p>

        <p>Une alerte a été déclenchée dans votre système de gestion de superette.</p>

        <div class="alert-details">
            <p><strong>Type d'Alerte :</strong> {{ $alertTypes[$alert->type] ?? 'Inconnu' }}</p>
            <p><strong>Catégorie concernée :</strong> {{ $alert->categorie ? $alert->categorie->nom : 'Toutes les catégories' }}</p>
            <p><strong>Seuil configuré :</strong> {{ number_format($alert->seuil, 2) }}</p>

            @if(in_array($alert->type, ['peremption', 'mouvement_important']))
                <p><strong>Période configurée :</strong> {{ $alert->periode }} jours</p>
            @endif

            <p><strong>Statut de l'alerte :</strong> {{ $alert->actif ? 'Active' : 'Inactive' }}</p>
            {{-- Ajoutez d'autres détails pertinents si nécessaire --}}
        </div>

        <p>Veuillez vous connecter au système pour vérifier les détails et prendre les mesures nécessaires.</p>

        {{-- Optionnel: Lien vers la page de l'alerte si les routes sont accessibles --}}
        {{-- <p><a href="{{ route('alertes.show', $alert) }}">Voir les détails de l'alerte dans le système</a></p> --}}

        <p>Cordialement,</p>
        <p>L'équipe {{ config('app.name') }}</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Veuillez ne pas y répondre.</p>
    </div>
</body>
</html> 