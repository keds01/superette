@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Détails du Document</h1>
            <div class="flex space-x-3">
                <a href="{{ route('documents-employes.edit', $document) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('documents-employes.download', $document) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>
                <form action="{{ route('documents-employes.destroy', $document) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de l'employé -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Employé</h2>
                        <p class="text-gray-600">{{ $document->employe->nom }} {{ $document->employe->prenom }}</p>
                    </div>

                    <!-- Type de document -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Type de Document</h2>
                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $document->type_libelle }}
                        </span>
                    </div>

                    <!-- Titre -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Titre</h2>
                        <p class="text-gray-600">{{ $document->titre }}</p>
                    </div>

                    <!-- Statut de confidentialité -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Confidentialité</h2>
                        @if($document->est_confidentiel)
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Confidentiel
                            </span>
                        @else
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Public
                            </span>
                        @endif
                    </div>

                    <!-- Dates -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Date du Document</h2>
                        <p class="text-gray-600">{{ $document->date_document->format('d/m/Y') }}</p>
                    </div>

                    <!-- Date d'expiration -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Date d'Expiration</h2>
                        @if($document->date_expiration)
                            <div class="flex items-center">
                                <span class="text-sm {{ $document->estExpire() ? 'text-red-600' : ($document->joursAvantExpiration() <= 30 ? 'text-yellow-600' : 'text-gray-600') }}">
                                    {{ $document->date_expiration->format('d/m/Y') }}
                                    @if($document->joursAvantExpiration() !== null)
                                        <span class="text-xs">({{ $document->joursAvantExpiration() }} jours)</span>
                                    @endif
                                </span>
                                @if($document->estExpire())
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Expiré
                                    </span>
                                @elseif($document->joursAvantExpiration() !== null && $document->joursAvantExpiration() <= 30)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Expire bientôt
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Valide
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500">Pas de date d'expiration</p>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                @if($document->description)
                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Description</h2>
                        <p class="text-gray-600 whitespace-pre-line">{{ $document->description }}</p>
                    </div>
                @endif

                <!-- Informations sur le fichier -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">Fichier</h2>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $document->fichier }}</p>
                                <p class="text-xs text-gray-500">
                                    Ajouté le {{ $document->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('documents-employes.download', $document) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="flex justify-start">
            <a href="{{ route('documents-employes.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection