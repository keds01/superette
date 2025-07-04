@extends('layouts.app')

@section('title', 'Liste des Supérettes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête avec titre et bouton d'action -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Supérettes</h1>
                <p class="mt-2 text-lg text-gray-500">Gérez toutes vos supérettes et leurs informations.</p>
            </div>
            <a href="{{ route('superettes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-plus"></i>
                Nouvelle Supérette
            </a>
        </div>

        <!-- Messages de notification -->
        @if(session("success"))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow animate-fade-in-down">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session("success") }}</span>
                </div>
            </div>
        @endif

        <!-- Tableau des supérettes -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            @if($superettes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-indigo-100">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Nom</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Adresse</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-indigo-50">
                            @foreach($superettes as $superette)
                                <tr class="hover:bg-indigo-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-indigo-900">{{ $superette->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $superette->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $superette->adresse ?? "-" }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($superette->actif)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i> Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <!-- Bouton Activer -->
                                        <a href="{{ route('superettes.activate', $superette->id) }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-indigo-600 border border-indigo-700 rounded-xl shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                            <i class="fas fa-check-circle mr-1"></i> Activer
                                        </a>
                                        
                                        <!-- Bouton Détails -->
                                        <a href="{{ route('superettes.show', $superette) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-blue-700 bg-blue-100 border border-blue-200 rounded-xl shadow hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            <i class="fas fa-eye mr-1"></i> Détails
                                        </a>
                                        
                                        <!-- Bouton Modifier -->
                                        <a href="{{ route('superettes.edit', $superette) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-yellow-800 bg-yellow-100 border border-yellow-200 rounded-xl shadow hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                                            <i class="fas fa-edit mr-1"></i> Modifier
                                        </a>
                                        
                                        <!-- Bouton Supprimer -->
                                        <form action="{{ route('superettes.destroy', $superette) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette supérette ?');">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-red-700 bg-red-100 border border-red-200 rounded-xl shadow hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <i class="fas fa-trash-alt mr-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $superettes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-indigo-400 text-6xl mb-6">
                        <i class="fas fa-store-slash"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">Aucune supérette disponible</h3>
                    <p class="text-gray-500 mb-6">Vous n'avez pas encore créé de supérette.</p>
                    <a href="{{ route('superettes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        Créer ma première supérette
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
