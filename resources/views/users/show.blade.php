@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 animate-fade-in-down">
            <div class="flex items-center gap-4">
                @php
                    $profile = $user->profile;
                    $photoUrl = $profile && $profile->photo ? $profile->photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
                @endphp
                <img class="h-20 w-20 rounded-full object-cover border-4 border-indigo-200 shadow" src="{{ $photoUrl }}" alt="Photo de profil">
                    <div>
                    <h1 class="text-3xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-2">
                        <i class="fas fa-user-circle text-indigo-400"></i> {{ $user->name }}
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                    <div class="flex gap-2 mt-2">
                        @if($user->actif)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Actif</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Inactif</span>
                        @endif
                        @if($user->role === 'super_admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-200 text-purple-800">Super Administrateur</span>
                        @elseif($user->role === 'admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-200 text-indigo-800">Administrateur</span>
                        @elseif($user->role === 'responsable')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-200 text-blue-800">Responsable</span>
                        @elseif($user->role === 'caissier')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-200 text-green-800">Caissier</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-800">{{ $user->role ?: 'Aucun rôle' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Carte infos principales -->
        <div class="bg-white rounded-2xl shadow-xl border border-indigo-50 p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->telephone ?? 'Non renseigné' }}</p>
                            </div>
                             <div>
                                <p class="text-sm font-medium text-gray-500">Adresse</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->adresse ?? 'Non renseignée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Créé le</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

        <!-- Section rôle et accès -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100 shadow">
                <h3 class="text-lg font-bold text-indigo-700 mb-2 flex items-center gap-2"><i class="fas fa-id-badge"></i> Rôle</h3>
                <p class="mb-2">
                    @if($user->role === 'super_admin')
                        Super Administrateur : <span class="text-xs text-gray-500">Accès complet à toutes les fonctionnalités du système</span>
                    @elseif($user->role === 'admin')
                        Administrateur : <span class="text-xs text-gray-500">Gestion complète sauf administration des utilisateurs</span>
                    @elseif($user->role === 'responsable')
                        Responsable : <span class="text-xs text-gray-500">Gestion des stocks et des ventes</span>
                    @elseif($user->role === 'caissier')
                        Caissier : <span class="text-xs text-gray-500">Accès limité aux ventes</span>
                        @else
                        <span class="text-xs text-gray-500">Aucun rôle attribué</span>
                        @endif
                </p>
                    </div>
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow">
                <h3 class="text-lg font-bold text-gray-700 mb-2 flex items-center gap-2"><i class="fas fa-unlock-alt"></i> Accès</h3>
                <ul class="list-disc pl-5 space-y-1 text-gray-600">
                    @if($user->isSuperAdmin())
                        <li>Administration des utilisateurs et des rôles</li>
                        <li>Gestion complète des produits et stocks</li>
                        <li>Gestion des ventes et transactions</li>
                        <li>Gestion des clients et fournisseurs</li>
                        <li>Accès aux rapports et statistiques</li>
                        <li>Configuration système</li>
                    @elseif($user->isAdmin())
                        <li>Gestion complète des produits et stocks</li>
                        <li>Gestion des ventes et transactions</li>
                        <li>Gestion des clients et fournisseurs</li>
                        <li>Accès aux rapports et statistiques</li>
                    @elseif($user->isResponsable())
                        <li>Gestion des produits et stocks</li>
                        <li>Gestion des ventes</li>
                        <li>Gestion des clients</li>
                        <li>Gestion des alertes</li>
                        <li>Gestion des catégories et unités</li>
                        <li>Gestion des remises</li>
                        <li>Suivi des mouvements de stock</li>
                    @elseif($user->isCaissier())
                        <li>Gestion des ventes</li>
                        <li>Consultation des produits</li>
                        <li>Consultation des clients</li>
                                @endif
                            </ul>
                    </div>
                </div>

        <!-- Historique des activités -->
        <div class="bg-white rounded-xl shadow border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-history"></i> Historique des activités</h3>
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
@endsection
