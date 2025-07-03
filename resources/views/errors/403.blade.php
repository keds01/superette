@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl overflow-hidden shadow-xl sm:rounded-2xl p-8">
            <div class="text-center">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H9m3-3V9m0 0V7m0 2h2m-2 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                
                <h2 class="mt-4 text-4xl font-bold text-red-600">Accès Refusé</h2>
                <p class="mt-2 text-lg text-gray-600">{{ $message ?? 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.' }}</p>
                
                @if(auth()->check() && auth()->user()->isCaissier())
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-yellow-700">En tant que caissier(e), vous avez uniquement accès aux pages suivantes :</p>
                    <ul class="mt-2 list-disc list-inside text-left max-w-md mx-auto">
                        <li>Gestion des ventes</li>
                        <li>Gestion des clients</li>
                        <li>Votre profil utilisateur</li>
                    </ul>
                </div>
                @endif

                @if(auth()->check() && auth()->user()->isResponsable())
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-blue-700">En tant que responsable, vous avez uniquement accès aux pages suivantes :</p>
                    <ul class="mt-2 list-disc list-inside text-left max-w-md mx-auto">
                        <li>Tableau de bord</li>
                        <li>Gestion des produits et stocks</li>
                        <li>Gestion des ventes</li>
                        <li>Gestion des clients</li>
                        <li>Gestion des alertes</li>
                        <li>Gestion des catégories</li>
                        <li>Gestion des unités</li>
                        <li>Gestion des remises</li>
                        <li>Votre profil utilisateur</li>
                    </ul>
                </div>
                @endif
                
                <div class="mt-8 flex flex-col sm:flex-row justify-center items-center gap-4">
                    @if(auth()->check() && auth()->user()->isCaissier())
                        <a href="{{ route('ventes.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-cash-register mr-2"></i> Aller aux ventes
                        </a>
                        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-users mr-2"></i> Aller aux clients
                        </a>
                    @elseif(auth()->check() && auth()->user()->isResponsable())
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-home mr-2"></i> Tableau de bord
                        </a>
                        <a href="{{ route('stocks.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-boxes mr-2"></i> Gérer les stocks
                        </a>
                        <a href="{{ route('ventes.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-cash-register mr-2"></i> Gérer les ventes
                        </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-home mr-2"></i> Retour au tableau de bord
                    </a>
                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-users-cog mr-2"></i> Gérer les utilisateurs
                    </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
