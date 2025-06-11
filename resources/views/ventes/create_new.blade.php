@extends('layouts.app')

@section('title', 'Nouvelle Vente')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header avec fil d'Ariane -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-indigo-600">
                        <i class="fas fa-home"></i>
                    </a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('ventes.index') }}" class="text-gray-500 hover:text-indigo-600">Ventes</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-medium">Nouvelle vente</span>
                </div>
                <a href="{{ route('ventes.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle vente</h1>
            <p class="mt-1 text-gray-600">Créez une nouvelle vente en complétant les informations ci-dessous</p>
        </div>
        
        <!-- Indicateurs d'étapes -->
        <div class="mb-8">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex flex-col items-center space-y-1">
                    <div id="step1-indicator" class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-medium shadow-md">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="text-sm font-medium text-indigo-600">Client</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2 relative">
                    <div id="progress-1-2" class="absolute inset-0 bg-indigo-500 transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex flex-col items-center space-y-1">
                    <div id="step2-indicator" class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-medium">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500">Produits</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2 relative">
                    <div id="progress-2-3" class="absolute inset-0 bg-indigo-500 transition-all duration-300" style="width: 0%"></div>
                </div>
                <div class="flex flex-col items-center space-y-1">
                    <div id="step3-indicator" class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-medium">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500">Validation</span>
                </div>
            </div>
        </div>
        
        <!-- Formulaire -->
        <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
            @csrf
            
            <!-- Messages d'alerte -->
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Étape 1: Sélection du client -->
            <div id="step1" class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user text-indigo-600"></i>
                            <span>Sélection du client</span>
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Recherche client -->
                            <div class="relative">
                                <label for="searchClient" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un client</label>
                                <div class="relative">
                                    <input type="text" id="searchClient" class="block w-full px-4 py-2 pl-10 pr-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Nom, prénom ou téléphone...">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sélection client -->
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="client_id" name="client_id" class="block w-full px-4 py-2 pr-8 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" data-email="{{ $client->email }}" data-telephone="{{ $client->telephone }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }} {{ $client->prenom }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations client -->
                        <div id="clientInfo" class="bg-gray-50 rounded-lg p-4 mb-6 hidden">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Informations du client</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Téléphone</p>
                                    <p id="clientTelephone" class="text-sm font-medium text-gray-900">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p id="clientEmail" class="text-sm font-medium text-gray-900">-</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bouton nouveau client -->
                        <div class="flex justify-between items-center">
                            <button type="button" id="btnNouveauClient" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-plus mr-2 text-indigo-600"></i>
                                Nouveau client
                            </button>
                            
                            <button type="button" id="btnStep1Next" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Continuer
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
