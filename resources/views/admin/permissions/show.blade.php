@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails de la Permission</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Bouton Retour -->
        <div class="mb-6">
            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour aux permissions
            </a>
        </div>

        <div class="mb-10">
            <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-purple-600 via-violet-500 to-indigo-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg mb-2">
                {{ isset($moduleInfo['module']) ? $moduleInfo['module'].' › ' : '' }}{{ $permission->description }}
            </h1>
            <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                <i class="fas fa-key text-purple-500 mr-2"></i>{{ $permission->name }}
            </div>
        </div>

        <!-- Grille d'informations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Carte détails permission avec glassmorphism -->
            <div class="bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-purple-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                    Informations sur la Permission
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Identifiant</h3>
                        <p class="mt-1 text-base font-medium text-gray-900">#{{ $permission->id }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Nom technique</h3>
                        <p class="mt-1 text-base font-medium text-indigo-700">{{ $permission->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $permission->description }}</p>
                    </div>

                    @if(isset($moduleInfo['module']))
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Structure</h3>
                        <div class="mt-1 space-y-1">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-indigo-700 w-24">Module:</span>
                                <span class="text-sm text-gray-700">{{ $moduleInfo['module'] }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-indigo-700 w-24">Action:</span>
                                <span class="text-sm text-gray-700">{{ $moduleInfo['action'] }}</span>
                            </div>
                            @if(isset($moduleInfo['sub_action']))
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-indigo-700 w-24">Sous-action:</span>
                                <span class="text-sm text-gray-700">{{ $moduleInfo['sub_action'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de création</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $permission->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dernière modification</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $permission->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </a>
                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Carte rôles associés avec glassmorphism -->
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-indigo-800 mb-4 flex items-center">
                    <i class="fas fa-user-tag mr-2 text-indigo-600"></i>
                    Rôles Associés ({{ $roles->count() }})
                </h2>
                
                @if($roles->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Cette permission est attribuée aux rôles suivants :</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($roles as $role)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-indigo-100 hover:border-indigo-300 transition-all">
                                <div>
                                    <span class="block text-base font-medium text-indigo-800">{{ $role->name }}</span>
                                    <span class="block text-sm text-gray-500 mt-1">{{ $role->description }}</span>
                                </div>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Cette permission n'est actuellement attribuée à aucun rôle.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Autres permissions du même module -->
        @if(isset($moduleInfo['module']) && $relatedPermissions->count() > 0)
        <div class="bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-xl p-6 mb-10">
            <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                <i class="fas fa-project-diagram mr-2 text-blue-600"></i>
                Autres Permissions du Module "{{ $moduleInfo['module'] }}"
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                @foreach($relatedPermissions as $relatedPerm)
                    <a href="{{ route('admin.permissions.show', $relatedPerm) }}" class="p-4 border border-blue-100 rounded-xl hover:bg-blue-50 transition-all flex items-start space-x-3">
                        <div class="text-blue-600 mt-1">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <p class="text-base font-medium text-blue-800">{{ $relatedPerm->description }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $relatedPerm->name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif


    </div>
</div>
@endsection
