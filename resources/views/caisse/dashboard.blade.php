<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Caisse - Superette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Barre de navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-cash-register text-2xl text-indigo-600"></i>
                        <span class="ml-2 text-xl font-bold text-gray-800">Caisse {{ $caisse->numero }}</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-4">
                        <i class="fas fa-user mr-2"></i>
                        {{ $caisse->nom }}
                    </span>
                    <form method="POST" action="{{ route('caisse.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Ventes du jour -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Ventes du jour
                                </dt>
                                <dd class="text-3xl font-semibold text-gray-900">
                                    {{ $stats['ventes_jour'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Montant du jour -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Montant du jour
                                </dt>
                                <dd class="text-3xl font-semibold text-gray-900">
                                    {{ number_format($stats['montant_jour'], 2) }} €
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solde de la caisse -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-wallet text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Solde de la caisse
                                </dt>
                                <dd class="text-3xl font-semibold text-gray-900">
                                    {{ number_format($caisse->solde_actuel, 2) }} €
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières ventes -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Dernières ventes
                </h3>
            </div>
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    N° Vente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stats['dernieres_ventes'] as $vente)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $vente->numero }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $vente->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($vente->montant_total, 2) }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $vente->statut === 'terminee' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($vente->statut) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('ventes.show', $vente) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye mr-1"></i>
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Aucune vente aujourd'hui
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('ventes.create') }}" class="bg-white shadow rounded-lg p-6 hover:bg-gray-50 transition duration-150">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Nouvelle vente</h3>
                        <p class="mt-1 text-sm text-gray-500">Créer une nouvelle vente</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('caisse.rapport') }}" class="bg-white shadow rounded-lg p-6 hover:bg-gray-50 transition duration-150">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Rapport de caisse</h3>
                        <p class="mt-1 text-sm text-gray-500">Voir le rapport journalier</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Vérification périodique de la session
        function checkSession() {
            $.get('{{ route("caisse.check-session") }}', function(response) {
                if (!response.authenticated) {
                    window.location.href = '{{ route("caisse.login") }}';
                }
            });
        }

        // Vérifier la session toutes les 5 minutes
        setInterval(checkSession, 300000);
    </script>
</body>
</html> 