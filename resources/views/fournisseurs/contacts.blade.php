@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Contacts du Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Liste de tous les contacts pour {{ $fournisseur->nom }}.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                 {{-- Lien pour ajouter un nouveau contact si la fonctionnalité existe --}}
                 {{-- <a href="{{ route('contacts_fournisseurs.create', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un contact
                </a> --}}
                 <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour au fournisseur
                </a>
            </div>
        </div>

        <!-- Tableau moderne Tailwind pour les contacts -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-200">
                <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Nom Complet</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Fonction</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Téléphone</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">Principal</th>
                        {{-- <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th> --}}{{-- Activer si fonctionnalités edit/delete contacts existent --}}
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-blue-50">
                    @forelse($contacts as $contact)
                            <tr class="hover:bg-blue-50/70 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-900">{{ $contact->nom }} {{ $contact->prenom }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $contact->fonction }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $contact->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $contact->telephone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($contact->est_principal)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Oui
                                        </span>
                                    @else
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Non
                                        </span>
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('contacts_fournisseurs.edit', [$fournisseur, $contact]) }}" 
                                           class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('contacts_fournisseurs.destroy', [$fournisseur, $contact]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition" title="Supprimer" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contact ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                     </div>
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-blue-400">Aucun contact trouvé pour ce fournisseur.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination moderne -->
                <div class="mt-6 flex justify-center">
                    {{ $contacts->links() }}
                </div>
            
            </div>
        </div>
    </div>

@push('styles')
<style>
/* Styles pour l'animation du header */
.animate-fade-in-down {
    animation: fadeInDown 0.5s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Style pour l'ombre néon (à adapter si nécessaire) */
.hover\:shadow-neon-blue:hover {
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); /* blue-500 */
}
</style>
@endpush
@endsection 