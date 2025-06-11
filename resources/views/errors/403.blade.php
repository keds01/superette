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
                
                @if(isset($permission))
                <div class="mt-4 p-4 bg-red-50 rounded-lg">
                    <p class="text-red-700">Permission requise: <strong>{{ $permission }}</strong></p>
                </div>
                @endif
                
                <div class="mt-8 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-home mr-2"></i> Retour au tableau de bord
                    </a>
                    @if(auth()->user() && auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-users-cog mr-2"></i> Gérer les utilisateurs
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
