@extends('layouts.app')

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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Documents des Employés
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">Gérez les documents de vos employés</p>
                    </div>
                    @if(isset($employe))
                        <a href="{{ route('documents-employes.create', $employe) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un Document
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filtres et Recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form action="{{ route('documents-employes.index') }}" method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70"
                                   placeholder="Titre, description...">
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de Document</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70">
                                <option value="">Tous les types</option>
                                <option value="cv" {{ request('type') == 'cv' ? 'selected' : '' }}>CV</option>
                                <option value="contrat" {{ request('type') == 'contrat' ? 'selected' : '' }}>Contrat</option>
                                <option value="diplome" {{ request('type') == 'diplome' ? 'selected' : '' }}>Diplôme</option>
                                <option value="certificat" {{ request('type') == 'certificat' ? 'selected' : '' }}>Certificat</option>
                                <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div>
                            <label for="confidentiel" class="block text-sm font-medium text-gray-700 mb-1">Confidentialité</label>
                            <select name="confidentiel" id="confidentiel" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70">
                                <option value="">Tous</option>
                                <option value="1" {{ request('confidentiel') == '1' ? 'selected' : '' }}>Confidentiels</option>
                                <option value="0" {{ request('confidentiel') == '0' ? 'selected' : '' }}>Non confidentiels</option>
                            </select>
                        </div>

                        <div>
                            <label for="expire_bientot" class="block text-sm font-medium text-gray-700 mb-1">Expiration</label>
                            <select name="expire_bientot" id="expire_bientot" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70">
                                <option value="">Tous</option>
                                <option value="30" {{ request('expire_bientot') == '30' ? 'selected' : '' }}>Expire dans 30 jours</option>
                                <option value="60" {{ request('expire_bientot') == '60' ? 'selected' : '' }}>Expire dans 60 jours</option>
                                <option value="90" {{ request('expire_bientot') == '90' ? 'selected' : '' }}>Expire dans 90 jours</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('documents-employes.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Réinitialiser
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Documents -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-indigo-100">
                    <thead class="bg-indigo-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'titre', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="flex items-center gap-1 hover:text-indigo-700 transition-colors">
                                    Titre
                                    @if(request('sort') == 'titre')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Employé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Date Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Expiration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-indigo-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-indigo-100">
                        @forelse($documents as $document)
                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($document->est_confidentiel)
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        @endif
                                        <span class="text-sm font-medium text-gray-900">{{ $document->titre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $document->employe->nom }} {{ $document->employe->prenom }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $document->type_libelle }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $document->date_document->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($document->date_expiration)
                                        <span class="text-sm {{ $document->estExpire() ? 'text-red-600' : ($document->joursAvantExpiration() <= 30 ? 'text-yellow-600' : 'text-gray-500') }}">
                                            {{ $document->date_expiration->format('d/m/Y') }}
                                            @if($document->joursAvantExpiration() !== null)
                                                <span class="text-xs">({{ $document->joursAvantExpiration() }} jours)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($document->estExpire())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Expiré
                                        </span>
                                    @elseif($document->joursAvantExpiration() !== null && $document->joursAvantExpiration() <= 30)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Expire bientôt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Valide
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('documents-employes.show', $document) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Voir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('documents-employes.edit', $document) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('documents-employes.download', $document) }}" 
                                           class="text-green-600 hover:text-green-900 transition-colors" title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('documents-employes.destroy', $document) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900">Aucun document trouvé</p>
                                        <p class="text-sm text-gray-500 mt-1">Commencez par ajouter un document</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection 