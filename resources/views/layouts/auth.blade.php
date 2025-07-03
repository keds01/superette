<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Authentification')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    @livewireStyles
    @stack('styles')
    
    <style>
        /* Styles personnalisés pour la page de connexion */
        .login-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #8b5cf6 100%);
        }
        .login-card {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .login-input {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
        }
        .login-input:focus {
            background-color: rgba(255, 255, 255, 1);
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25);
        }
        .login-button {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            transition: all 0.3s ease;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }
    </style>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/LOGO_ELIFRANC_PRIX.png">
</head>
<body class="font-sans antialiased flex items-center justify-center min-h-screen login-gradient">
    <main class="w-full">
        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
