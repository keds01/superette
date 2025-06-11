@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Permissions</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne avec titre gradient -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-purple-600 via-violet-500 to-indigo-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Permissions</h1>
                <p class="mt-2 text-lg text-gray-500">Configurez les permissions du système pour contrôler l'accès aux fonctionnalités.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.permissions.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajouter une permission
                </a>
            </div>
        </div>

        <!-- Statistiques glassmorphism -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Total permissions</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $permissions->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700 flex items-center gap-1">Modules représentés <i class="fas fa-info-circle text-indigo-500" data-bs-toggle="tooltip" title="Nombre de modules différents ayant des permissions configurées dans le système"></i></h3>
                @php
                    $modules = $permissions->map(function($permission) {
                        $parts = explode('.', $permission->name);
                        return $parts[0] ?? null;
                    })->filter()->unique()->count();
                @endphp
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $modules }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-violet-700 flex items-center gap-1">Rôles associés <i class="fas fa-info-circle text-violet-500" data-bs-toggle="tooltip" title="Nombre moyen de rôles qui ont des permissions associées"></i></h3>
                @php
                    $totalRoleAssocs = 0;
                    foreach($permissions as $permission) {
                        $totalRoleAssocs += $permission->roles()->count();
                    }
                    $averageRoles = $permissions->count() > 0 ? round($totalRoleAssocs / $permissions->count(), 1) : 0;
                @endphp
                <p class="text-3xl font-bold text-violet-900 mt-2">{{ $averageRoles }} <span class="text-sm text-violet-700">par permission</span></p>
            </div>
        </div>

        <!-- Tableau Permissions avec glassmorphism -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-x-auto shadow-2xl ring-1 ring-purple-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        @if(session('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-4 mt-4 rounded-lg" role="alert">
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif
                        <table class="min-w-full divide-y divide-purple-200">
                            <thead class="bg-purple-50">
                                <tr>
                                    <th scope="col" class="py-3.5 px-4 text-sm font-semibold text-left text-purple-900">ID</th>
                                    <th scope="col" class="py-3.5 px-4 text-sm font-semibold text-left text-purple-900">Nom technique</th>
                                    <th scope="col" class="py-3.5 px-4 text-sm font-semibold text-left text-purple-900">Description</th>
                                    <th scope="col" class="py-3.5 px-4 text-sm font-semibold text-center text-purple-900">Rôles associés</th>
                                    <th scope="col" class="relative py-3.5 px-4 text-center">
                                        <span class="text-sm font-semibold text-purple-900">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-purple-100">
                                @forelse($permissions as $permission)
                                    <tr class="hover:bg-purple-50 transition-colors">
                                        <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-purple-900">{{ $permission->id }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-sm text-indigo-700 font-medium">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-key text-purple-500"></i>
                                                {{ $permission->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $permission->description }}</td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $permission->roles->count() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('admin.permissions.show', $permission) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Modifier permission">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')" title="Supprimer permission">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-sm text-gray-500 text-center">Aucune permission trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
