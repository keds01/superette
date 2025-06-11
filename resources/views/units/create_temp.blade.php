@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Ajouter une unité</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Créez une nouvelle unité de mesure pour vos produits. Tous les champs marqués d'un astérisque (*) sont obligatoires.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:col-span-2 md:mt-0">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf
                        <div class="shadow sm:overflow-hidden sm:rounded-md">
                            <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                                <div>
                                    <label for="nom" class="form-label">Nom de l'unité *</label>
                                    <input type="text" name="nom" id="nom" class="form-input mt-1" value="{{ old('nom') }}" required>
                                    <p class="mt-1 text-sm text-gray-500">Exemple : Kilogramme, Litre, Pièce</p>
                                    @error('nom')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="symbole" class="form-label">Symbole *</label>
                                    <input type="text" name="symbole" id="symbole" class="form-input mt-1" value="{{ old('symbole') }}" required>
                                    <p class="mt-1 text-sm text-gray-500">Exemple : kg, L, pcs</p>
                                    @error('symbole')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="3" class="form-input mt-1">{{ old('description') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Une brève description de cette unité de mesure.</p>
                                    @error('description')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                                <a href="{{ route('units.index') }}" class="btn-secondary mr-2">
                                    Annuler
                                </a>
                                <button type="submit" class="btn-primary">
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
