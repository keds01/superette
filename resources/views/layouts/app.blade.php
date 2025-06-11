<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }" x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Gestion Superette')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        .sidebar-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar-scrollable::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .sidebar-scrollable::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Active link styling */
        .sidebar-link.active {
            background: #eef2ff;
            color: #4338ca !important;
            font-weight: 600;
            border-left: 4px solid #6366f1;
        }
        
        .sidebar-link:not(.active):hover {
            background: #f3f4f6;
            color: #4f46e5;
        }

        .sidebar-submenu-link.active {
            background: #eef2ff;
            color: #4338ca;
            font-weight: 500;
        }
        
        .sidebar-submenu-link:not(.active):hover {
            background: #f3f4f6;
            color: #4f46e5;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col flex-shrink-0 w-64 overflow-y-auto transition-transform duration-300 ease-in-out transform bg-white shadow-lg lg:translate-x-0 lg:static lg:inset-0"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            aria-label="Sidebar"
        >
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 border-b bg-white">
                <a href="{{ route('dashboard') }}" class="flex items-center text-xl font-bold text-indigo-600 gap-2">
                    <i class="fas fa-store text-2xl"></i>
                    <span>SuperetteApp</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto sidebar-scrollable">
                @php
                    $navLinksGroups = [
                        [
                            ['route' => 'dashboard', 'label' => 'Tableau de Bord', 'icon' => 'tachometer-alt', 'active_routes' => ['dashboard']],
                            ['route' => 'caisse.index', 'label' => 'Caisse', 'icon' => 'cash-register', 'active_routes' => ['caisse.*']],
                            ['route' => 'ventes.index', 'label' => 'Ventes', 'icon' => 'shopping-cart', 'active_routes' => ['ventes.*']],
                            ['route' => 'produits.index', 'label' => 'Produits', 'icon' => 'box-open', 'active_routes' => ['produits.*']],
                            ['route' => 'stocks.index', 'label' => 'Stocks', 'icon' => 'warehouse', 'active_routes' => ['stocks.*']],
                            ['route' => 'alertes.index', 'label' => 'Alertes', 'icon' => 'exclamation-triangle', 'active_routes' => ['alertes.*']],
                            ['route' => 'categories.index', 'label' => 'Catégories', 'icon' => 'tags', 'active_routes' => ['categories.*']],
                            ['route' => 'unites.index', 'label' => 'Unités', 'icon' => 'ruler', 'active_routes' => ['unites.*']],
                            ['route' => 'promotions.index', 'label' => 'Promotions', 'icon' => 'percent', 'active_routes' => ['promotions.*']],
                            ['route' => 'remises.index', 'label' => 'Remises', 'icon' => 'tag', 'active_routes' => ['remises.*']],
                            ['route' => 'clients.index', 'label' => 'Clients', 'icon' => 'users', 'active_routes' => ['clients.*']],
                            ['route' => 'commandes.index', 'label' => 'Commande', 'icon' => 'file-invoice', 'active_routes' => ['commandes.*']],
                            ['route' => 'reports.index', 'label' => 'Rapports', 'icon' => 'chart-bar', 'active_routes' => ['reports.*']],
                            ['route' => 'statistiques.index', 'label' => 'Statistiques', 'icon' => 'chart-line', 'active_routes' => ['statistiques.*']],
                            ['route' => 'mouvements.index', 'label' => 'Mouvements de stock', 'icon' => 'exchange-alt', 'active_routes' => ['mouvements.*']],
                            ['route' => 'employes.index', 'label' => 'Employés', 'icon' => 'user-tie', 'active_routes' => ['employes.*']],
                            ['route' => 'audit.index', 'label' => 'Audit', 'icon' => 'clipboard-check', 'active_routes' => ['audit.*']],
                            ['route' => 'admin.users.index', 'label' => 'Administration', 'icon' => 'cogs', 'active_routes' => ['admin.*']],
                        ],
                    ];
                @endphp

                @foreach ($navLinksGroups as $groupIndex => $navLinks)
                    @if($groupIndex > 0)
                        <div class="my-2 border-t border-gray-200"></div>
                    @endif
                    @foreach ($navLinks as $item)
                        @if(Route::has($item['route']))
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out sidebar-link {{ isLinkActive($item['active_routes']) ? 'active' : 'text-gray-600' }}">
                            <i class="fas fa-{{ $item['icon'] }} w-5 h-5 mr-3" aria-hidden="true"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                        @endif
                    @endforeach
                @endforeach
            </nav>

            <!-- User Menu -->
            @auth
            <div class="p-4 border-t">
                <div class="flex items-center">
                    <img class="w-8 h-8 rounded-full mr-3" 
                         src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                         alt="{{ Auth::user()->name }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ml-2 text-gray-400 hover:text-gray-500">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Backdrop for mobile sidebar -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden" x-cloak></div>

        <!-- Main content -->
        <div class="flex-1">
            <!-- Top bar -->
            <header class="sticky top-0 z-20 flex items-center justify-between h-16 px-4 bg-white border-b shadow-sm">
                <div class="flex items-center">
                    <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="ml-4">
                        @yield('header')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
