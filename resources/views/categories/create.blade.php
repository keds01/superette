@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-indigo-900">Nouvelle Catégorie</h2>
                    <p class="mt-2 text-gray-600">Créez une nouvelle catégorie pour organiser vos produits.</p>
                </div>

                <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la catégorie</label>
                            <input type="text" 
                                   name="nom" 
                                   id="nom" 
                                   value="{{ old('nom') }}"
                                   class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   required
                                   minlength="3"
                                   maxlength="50">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="actif" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="actif" 
                                    id="actif"
                                    class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="1" {{ old('actif', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('actif') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('actif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                  required
                                  minlength="10"
                                  maxlength="500">{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Minimum 10 caractères, maximum 500 caractères.</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-end space-x-4 pt-6">
                        <a href="{{ route('categories.index') }}" 
                           class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            Créer la catégorie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const nomInput = document.getElementById('nom');
        const descriptionInput = document.getElementById('description');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessage = '';

            // Validation du nom
            if (nomInput.value.length < 3) {
                isValid = false;
                errorMessage = 'Le nom doit contenir au moins 3 caractères.';
            } else if (nomInput.value.length > 50) {
                isValid = false;
                errorMessage = 'Le nom ne peut pas dépasser 50 caractères.';
            }

            // Validation de la description
            if (descriptionInput.value.length < 10) {
                isValid = false;
                errorMessage = 'La description doit contenir au moins 10 caractères.';
            } else if (descriptionInput.value.length > 500) {
                isValid = false;
                errorMessage = 'La description ne peut pas dépasser 500 caractères.';
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
            }
        });
    });
    </script>
    @endpush
@endsection 