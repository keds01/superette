<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Caisse - Superette</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
        }
        
        .glassmorphism {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }
        
        .stat-card {
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
            opacity: 0;
        }
        
        .stat-card:hover::before {
            animation: shine 1.5s ease;
        }
        
        @keyframes shine {
            0% {
                opacity: 0;
                left: -50%;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                left: 150%;
            }
        }
        
        .action-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .nav-glassmorphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Barre de navigation -->
    <nav class="nav-glassmorphism sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-2 rounded-lg text-white">
                            <i class="fas fa-cash-register text-xl"></i>
                        </div>
                        <span class="ml-3 text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                            Caisse {{ $caisse->numero }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex items-center text-gray-700">
                        <div class="bg-indigo-100 p-2 rounded-full">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <span class="ml-2 font-medium">{{ $caisse->nom }}</span>
                    </span>
                    <form method="POST" action="{{ route('caisse.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center text-gray-600 hover:text-red-600 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <!-- Messages -->
        @if (session('success'))
            <div class="glassmorphism border-l-4 border-green-500 p-4 mb-6 rounded-lg animate-fade-in" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="glassmorphism border-l-4 border-red-500 p-4 mb-6 rounded-lg animate-fade-in" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions rapides principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('ventes.create') }}" class="action-card glassmorphism group">
                <div class="bg-gradient-to-tr from-green-500 to-emerald-600 p-6 text-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Nouvelle Vente</h3>
                            <p class="text-white/80">Enregistrer une nouvelle transaction</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-2xl group-hover:translate-x-2 transition-transform duration-300"></i>
                </div>
            </a>

            <a href="{{ route('caisse.rapport') }}" class="action-card glassmorphism group">
                <div class="bg-gradient-to-tr from-blue-500 to-indigo-600 p-6 text-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-chart-bar text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Rapport de Caisse</h3>
                            <p class="text-white/80">Consulter le bilan journalier</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-2xl group-hover:translate-x-2 transition-transform duration-300"></i>
                </div>
            </a>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Ventes du jour -->
            <div class="stat-card glassmorphism rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-tr from-indigo-500/10 to-purple-500/10 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Ventes du jour
                            </p>
                            <div class="flex items-baseline">
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ $stats['ventes_jour'] }}
                                </p>
                                <p class="ml-2 text-sm text-green-600 font-medium">transactions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Montant du jour -->
            <div class="stat-card glassmorphism rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-tr from-green-500/10 to-emerald-500/10 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-tr from-green-500 to-emerald-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Montant du jour
                            </p>
                            <div class="flex items-baseline">
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ number_format($stats['montant_jour'], 0, ',', ' ') }}
                                </p>
                                <p class="ml-2 text-sm text-green-600 font-medium">FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solde de la caisse -->
            <div class="stat-card glassmorphism rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-tr from-blue-500/10 to-sky-500/10 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-tr from-blue-500 to-sky-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-wallet text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Solde de la caisse
                            </p>
                            <div class="flex items-baseline">
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ number_format($caisse->solde_actuel, 0, ',', ' ') }}
                                </p>
                                <p class="ml-2 text-sm text-blue-600 font-medium">FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières ventes -->
        <div class="glassmorphism rounded-2xl overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-200/50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-history text-indigo-600 mr-3"></i>
                    Dernières ventes
                </h3>
                <a href="{{ route('ventes.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center text-sm font-medium">
                    Voir tout
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/50">
                    <thead>
                        <tr class="bg-gray-50/50">
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
                    <tbody class="divide-y divide-gray-200/50">
                        @forelse($stats['dernieres_ventes'] as $vente)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $vente->numero }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vente->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $vente->statut === 'terminee' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($vente->statut) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('ventes.show', $vente) }}" class="btn-gradient text-white px-3 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                                        <p>Aucune vente aujourd'hui</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions secondaires -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('clients.index') }}" class="glassmorphism rounded-xl p-4 flex flex-col items-center justify-center hover:bg-indigo-50 transition-colors group">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full group-hover:bg-indigo-200 transition-colors">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-700">Clients</span>
            </a>
            
            <a href="{{ route('produits.index') }}" class="glassmorphism rounded-xl p-4 flex flex-col items-center justify-center hover:bg-blue-50 transition-colors group">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-700">Produits</span>
            </a>
            
            <a href="{{ route('remises.index') }}" class="glassmorphism rounded-xl p-4 flex flex-col items-center justify-center hover:bg-green-50 transition-colors group">
                <div class="bg-green-100 text-green-600 p-3 rounded-full group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-tag text-xl"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-700">Remises</span>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="glassmorphism rounded-xl p-4 flex flex-col items-center justify-center hover:bg-purple-50 transition-colors group">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-user-cog text-xl"></i>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-700">Profil</span>
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Animation des éléments au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.stat-card, .action-card, .glassmorphism');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('animate-fade-in');
                }, index * 100);
            });
        });
        
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