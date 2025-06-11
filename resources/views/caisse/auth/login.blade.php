<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Caisse - Superette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo ou Titre -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Connexion Caisse</h1>
                <p class="text-gray-600 mt-2">Entrez vos identifiants pour accéder à votre caisse</p>
            </div>

            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Message de succès -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulaire de connexion -->
            <form method="POST" action="{{ route('caisse.login') }}" class="space-y-6">
                @csrf
                
                <!-- Numéro de caisse -->
                <div>
                    <label for="numero_caisse" class="block text-sm font-medium text-gray-700">
                        Numéro de Caisse
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-cash-register text-gray-400"></i>
                        </div>
                        <input type="text" name="numero_caisse" id="numero_caisse" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                               value="{{ old('numero_caisse') }}"
                               placeholder="Entrez le numéro de caisse"
                               required>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                               placeholder="Entrez votre mot de passe"
                               required>
                    </div>
                </div>

                <!-- Bouton de connexion -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Lien de retour -->
            <div class="mt-6 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Animation de chargement lors de la soumission du formulaire
        $('form').on('submit', function() {
            $(this).find('button[type="submit"]').html(
                '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion en cours...'
            ).prop('disabled', true);
        });
    </script>
</body>
</html> 