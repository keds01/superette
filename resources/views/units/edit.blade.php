@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier l'unité</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Modifiez les informations de l'unité de mesure. Tous les champs marqués d'un astérisque (*) sont obligatoires.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:col-span-2 md:mt-0">
                    <form action="{{ route('unites.update', $unite->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="shadow sm:overflow-hidden sm:rounded-md">
                            <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                                <div>
                                    <label for="nom" class="form-label">Nom de l'unité *</label>
                                    <input type="text" name="nom" id="nom" class="form-input mt-1" value="{{ old('nom', $unite->nom) }}" required>
                                    <p class="mt-1 text-sm text-gray-500">Exemple : Kilogramme, Litre, Pièce</p>
                                    @error('nom')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="symbole" class="form-label">Symbole *</label>
                                    <input type="text" name="symbole" id="symbole" class="form-input mt-1" value="{{ old('symbole', $unite->symbole) }}" required>
                                    <p class="mt-1 text-sm text-gray-500">Exemple : kg, L, pcs</p>
                                    @error('symbole')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="3" class="form-input mt-1">{{ old('description', $unite->description) }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Une brève description de cette unité de mesure.</p>
                                    @error('description')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if($unite->products_count > 0)
                                    <div class="rounded-md bg-blue-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700">
                                                    Cette unité est utilisée par {{ $unite->products_count }} produit(s). La modification n'affectera pas les produits existants.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                                <a href="{{ route('unites.index') }}" class="btn-secondary mr-2">
                                    Annuler
                                </a>
                                <button type="submit" class="btn-primary">
                                    Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 