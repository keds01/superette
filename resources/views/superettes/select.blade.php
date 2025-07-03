@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Sélection de Superette</h1>
                <p class="mt-2 text-lg text-gray-500">Choisissez la superette avec laquelle vous souhaitez travailler.</p>
            </div>
            @if(auth()->check())
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('superettes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouvelle Superette
                </a>
            </div>
            @endif
        </div>

        @if(session('info'))
            <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                        </div>
                    @endif

        @if(isset($error))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ $error }}</span>
                </div>
            </div>
        @endif

        <!-- Tableau des superettes -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 overflow-x-auto">
            <table class="min-w-full divide-y divide-indigo-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Adresse</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider w-56">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-indigo-100">
                    @forelse($superettes as $superette)
                        <tr class="hover:bg-indigo-50/70 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-indigo-900">{{ $superette->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $superette->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $superette->adresse ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-nowrap justify-center gap-1">
                                    <a href="{{ route('superettes.activate', $superette->id) }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-indigo-600 border border-indigo-700 rounded-xl shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400 transition" title="Sélectionner">
                                        <i class="fas fa-check-circle mr-2"></i> Sélectionner
                                    </a>
                                    
                                    @if(auth()->check())
                                        <a href="{{ route('superettes.show', $superette) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-blue-700 bg-blue-100 border border-blue-200 rounded-xl shadow hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('superettes.edit', $superette) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-yellow-800 bg-yellow-100 border border-yellow-200 rounded-xl shadow hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition" title="Modifier">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('superettes.destroy', $superette) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette superette ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-bold text-red-800 bg-red-100 border border-red-200 rounded-xl shadow hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune superette trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 