@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-8 md:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Créer un nouveau Client
                </h1>
                <p class="mt-3 text-gray-600">Remplissez les informations ci-dessous pour ajouter un nouveau client.</p>
            </div>

            <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="nom" 
                               id="nom" 
                               value="{{ old('nom') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                               required>
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" 
                               name="prenom" 
                               id="prenom" 
                               value="{{ old('prenom') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm">
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="telephone" 
                               id="telephone" 
                               value="{{ old('telephone') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                               placeholder="+228 XX XX XX XX"
                               required>
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                               placeholder="exemple@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type de client</label>
                        <select name="type" 
                                id="type"
                                class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm">
                            <option value="particulier" {{ old('type') == 'particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="entreprise" {{ old('type') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700">Statut <span class="text-red-500">*</span></label>
                        <select name="statut" 
                                id="statut"
                                class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                                required>
                            <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('statut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" 
                           name="adresse" 
                           id="adresse" 
                           value="{{ old('adresse') }}"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                           placeholder="Quartier, rue, ville...">
                    @error('adresse')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="4"
                              class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                              placeholder="Informations supplémentaires...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <a href="{{ route('clients.index') }}" 
                       class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Créer le client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 