<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        h1 {
            color: #4338ca;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Page de Test</h1>
        <p>Cette page de test s'affiche correctement.</p>
        <p>Cela signifie que le routage et le rendu des vues fonctionnent.</p>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e0f2fe; border-radius: 6px;">
            <h2>Diagnostic</h2>
            <p>Si cette page s'affiche mais pas la page des superettes, le problème est probablement lié à:</p>
            <ul>
                <li>La vue spécifique des superettes</li>
                <li>Le contrôleur SuperetteController</li>
                <li>Les middlewares appliqués à la route des superettes</li>
            </ul>
        </div>
    </div>
</body>
</html>
