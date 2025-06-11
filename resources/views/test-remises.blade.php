<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Remises</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h1 {
            color: #333;
        }
        .info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">
            <h2>✅ Test réussi!</h2>
            <p>Cette page s'affiche correctement, ce qui signifie que le problème n'est pas lié aux routes.</p>
        </div>

        <div class="info">
            <h3>Informations de diagnostic</h3>
            <p>Date et heure : {{ date('Y-m-d H:i:s') }}</p>
            <p>Version de l'application : {{ app()->version() }}</p>
            <p>Environnement : {{ app()->environment() }}</p>
        </div>

        <h1>Page de test des remises</h1>
        <p>Cette page est une version simplifiée pour diagnostiquer pourquoi la page des remises apparaît blanche.</p>
        
        <h3>Liens de navigation</h3>
        <ul>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li><a href="{{ url('/dashboard') }}">Tableau de bord</a></li>
        </ul>
    </div>
</body>
</html>
