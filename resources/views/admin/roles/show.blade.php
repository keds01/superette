@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails du Rôle</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Bouton Retour -->
        <div class="mb-6">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour aux rôles
            </a>
        </div>

        <!-- Titre principal -->
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg mb-2">{{ $role->name }}</h1>
            <p class="mt-2 text-lg text-gray-600">{{ $role->description }}</p>
        </div>

        <!-- Grille d'informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Carte détails du rôle avec glassmorphism -->
            <div class="bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-id-badge mr-2 text-blue-600"></i>
                    Informations sur le Rôle
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Identifiant</h3>
                        <p class="mt-1 text-base font-medium text-gray-900">#{{ $role->id }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nom</h3>
                        <p class="mt-1 text-base font-medium text-indigo-700">{{ $role->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $role->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nombre d'utilisateurs</h3>
                        <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $users->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $users->count() }} {{ $users->count() <= 1 ? 'utilisateur' : 'utilisateurs' }}
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nombre de permissions</h3>
                        <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ collect($permissionsByModule)->flatten()->count() }} permissions
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de création</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $role->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dernière modification</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $role->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </a>
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.')">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Carte utilisateurs associés avec glassmorphism -->
            <div class="bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-green-600"></i>
                    Utilisateurs avec ce rôle ({{ $users->count() }})
                </h2>
                
                @if($users->count() > 0)
                    <div class="max-h-60 overflow-y-auto pr-2 mt-4">
                        <div class="space-y-3">
                            @foreach($users as $user)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-green-100 hover:border-green-300 transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-500 text-white flex items-center justify-center">
                                            <span class="font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Ce rôle n'est actuellement attribué à aucun utilisateur.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Permissions compactes par module -->
        <div class="mb-6">
            <h2 class="text-lg font-bold text-purple-800 flex items-center mb-2">
                <i class="fas fa-key mr-2"></i>
                Permissions Associées ({{ collect($permissionsByModule)->flatten()->count() }})
                <span class="ml-2 text-xs text-gray-400" title="Cliquez sur un module pour voir le détail">
                    <i class="fas fa-info-circle"></i>
                </span>
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach($permissionsByModule as $module => $modulePermissions)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="bg-purple-100 text-purple-700 rounded-full px-4 py-1 font-semibold flex items-center hover:bg-purple-200 transition">
                            <i class="fas fa-layer-group mr-1"></i> {{ $module }}
                            <span class="ml-2 bg-white text-purple-700 rounded-full px-2 text-xs font-bold">{{ count($modulePermissions) }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute z-30 mt-2 w-64 bg-white border border-purple-100 rounded-lg shadow-lg p-3">
                            <ul>
                                @foreach($modulePermissions as $perm)
                                    <li class="flex items-center py-1 text-sm">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span>{{ $perm->description }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
