@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h.01M7 16h.01M12 13.25a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M10 5a2 2 0 114 0 2 2 0 01-4 0z" />
                            </svg>
                            Détails de l'Utilisateur
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ $user->name }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('users.edit', $user) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                         @if(!$user->hasRole('Administrateur'))
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white font-semibold shadow-lg hover:bg-red-700 transition-colors duration-200" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                <i class="fas fa-trash"></i>
                                Supprimer
                            </button>
                        @endif
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informations personnelles
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nom</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->telephone ?? 'Non renseigné' }}</p>
                            </div>
                             <div>
                                <p class="text-sm font-medium text-gray-500">Adresse</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->adresse ?? 'Non renseignée' }}</p>
                            </div>
                             <div>
                                <p class="text-sm font-medium text-gray-500">Statut</p>
                                <p class="mt-1 text-lg font-semibold">
                                    @if($user->actif)
                                        <span class="text-green-600">Actif</span>
                                    @else
                                        <span class="text-red-600">Inactif</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Créé le</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rôles et permissions -->
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0h4m-6 9h6m-6 3h6" />
                            </svg>
                            Rôles attribués
                        </h3>
                        @if($user->roles->isEmpty())
                            <div class="text-gray-500">Cet utilisateur n'a aucun rôle attribué.</div>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">{{ $role->nom }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                 <!-- Permissions héritées (simplifié) -->
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                           <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v5a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm0 0V5a2 2 0 10-4 0v2m4 0h-4" />
                            </svg>
                            Permissions héritées (par rôles)
                        </h3>
                         @php
                            $permissions = collect([]);
                            foreach($user->roles as $role) {
                                $permissions = $permissions->merge($role->permissions);
                            }
                            $permissions = $permissions->unique('id');
                        @endphp
                        @if($permissions->isEmpty())
                             <div class="text-gray-500">Aucune permission héritée par les rôles.</div>
                        @else
                            <ul class="list-disc list-inside space-y-2">
                                @foreach($permissions->take(10) as $permission)
                                    <li>
                                        {{ $permission->description }} 
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $permission->nom }}</span>
                                    </li>
                                @endforeach
                                 @if($permissions->count() > 10)
                                    <li>
                                        <em>Et {{ $permissions->count() - 10 }} autres permissions...</em>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>

                 <!-- Historique des activités (similaire aux mouvements de stock) -->
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                         <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historique des activités
                        </h3>
                         @if(isset($activities) && $activities->count() > 0)
                             <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-indigo-100">
                                    <thead class="bg-indigo-50/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Action</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Détails</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-indigo-100">
                                        @foreach($activities as $activity)
                                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $activity->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $activity->description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $activity->properties }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-4">Aucune activité enregistrée pour cet utilisateur.</p>
                        @endif
                    </div>
                 </div>

            </div>

            <!-- Colonne latérale (si nécessaire, par exemple pour des statistiques ou d'autres infos) -->
            <div class="space-y-6">
                 {{-- Vous pouvez ajouter ici des cartes pour des statistiques ou d'autres informations spécifiques à l'utilisateur si nécessaire --}}
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                     <div class="p-6">
                         <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                             <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                             </svg>
                             Plus d'informations
                         </h3>
                         <div class="text-gray-500">
                             {{-- Ajoutez ici d'autres informations ou statistiques pertinentes --}}
                             <p>Date de création du compte: {{ $user->created_at->format('d/m/Y') }}</p>
                             {{-- ... autres infos ... --}}
                         </div>
                     </div>
                 </div>

            </div>
        </div>

        <!-- Modal de confirmation de suppression (adapté pour Tailwind) -->
        @if(!$user->hasRole('Administrateur'))
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ?</p>
                        <p class="text-danger">Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
