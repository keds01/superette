@extends('layouts.app')

@section('title', 'Liste des Utilisateurs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-purple-500 to-pink-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Liste des Utilisateurs
                </h1>
                <p class="mt-2 text-lg text-gray-500">Gérez les comptes utilisateurs, rôles et accès à la plateforme.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-pink-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-user-plus"></i>
                    Nouvel Utilisateur
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6">
            <div class="rounded-xl bg-gradient-to-tr from-green-100 via-green-50 to-white border border-green-200 px-6 py-4 text-green-800 font-semibold shadow-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Tableau glassy -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-indigo-100">
            <table class="min-w-full divide-y divide-indigo-200">
                <thead class="bg-gradient-to-tr from-indigo-100 to-pink-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Rôles</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-indigo-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-indigo-50/70 transition-all">
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->telephone ?? 'Non renseigné' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'super_admin')
                                <span class="inline-block px-3 py-1 rounded-full bg-purple-200 text-purple-800 text-xs font-bold mr-2">Super Administrateur</span>
                            @elseif($user->role === 'admin')
                                <span class="inline-block px-3 py-1 rounded-full bg-indigo-200 text-indigo-800 text-xs font-bold mr-2">Administrateur</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full bg-gray-200 text-gray-800 text-xs font-bold mr-2">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->actif)
                                <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold text-xs">Actif</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold text-xs">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('users.show', $user->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 hover:bg-indigo-200 text-indigo-600 transition" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-50 hover:bg-yellow-200 text-yellow-600 transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 hover:bg-red-200 text-red-600 transition" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="flex flex-col items-center text-indigo-400">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span class="font-semibold">Aucun utilisateur trouvé.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination moderne -->
        <div class="mt-6 flex justify-center">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation des tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
