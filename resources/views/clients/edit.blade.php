@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Client') }}
        </h2>
        <a href="{{ route('clients.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nom">
                                    Nom * <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Nom du client, obligatoire pour l'identification"></i>
                                </x-input-label>
                                <x-text-input id="nom" class="block mt-1 w-full" type="text" name="nom" :value="old('nom', $client->nom)" required autofocus />
                                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="prenom">
                                    Prénom <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Prénom du client"></i>
                                </x-input-label>
                                <x-text-input id="prenom" class="block mt-1 w-full" type="text" name="prenom" :value="old('prenom', $client->prenom)" />
                                <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="telephone">
                                    Téléphone * <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Numéro de téléphone pour contacter le client, format togolais recommandé"></i>
                                </x-input-label>
                                <x-text-input id="telephone" class="block mt-1 w-full" type="text" name="telephone" :value="old('telephone', $client->telephone)" required />
                                <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="email">
                                    Email <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Adresse email pour l'envoi de factures et communications"></i>
                                </x-input-label>
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $client->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            
                            <div class="md:col-span-2">
                                <x-input-label for="adresse">
                                    Adresse <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Adresse physique du client, utile pour les livraisons"></i>
                                </x-input-label>
                                <x-text-input id="adresse" class="block mt-1 w-full" type="text" name="adresse" :value="old('adresse', $client->adresse)" />
                                <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                            </div>
                            
                            <div class="md:col-span-2">
                                <x-input-label for="notes">
                                    Notes <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Informations supplémentaires sur le client, préférences, etc."></i>
                                </x-input-label>
                                <textarea id="notes" name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('notes', $client->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="statut">
                                    Statut * <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Statut du client dans votre système (actif/inactif)"></i>
                                </x-input-label>
                                <select id="statut" name="statut" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="actif" {{ old('statut', $client->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ old('statut', $client->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="type">
                                    Type de client <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Type de client : particulier ou entreprise"></i>
                                </x-input-label>
                                <select id="type" name="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="particulier" {{ old('type', $client->type) == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                    <option value="entreprise" {{ old('type', $client->type) == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button type="button" onclick="window.location='{{ route('clients.index') }}'">
                                Annuler
                            </x-secondary-button>
                            
                            <x-primary-button class="ml-4">
                                Mettre à jour
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
