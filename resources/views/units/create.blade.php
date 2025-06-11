@extends('layouts.app')

@section('content')
    <meta http-equiv="refresh" content="0;url={{ url('/admin/users') }}">
    <div class="flex flex-col items-center justify-center min-h-[40vh]">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-700 mt-10 mb-4">
                Cette page n’existe plus !
            </h2>
            <p class="mb-4 text-gray-500">Vous allez être redirigé automatiquement vers la nouvelle gestion des utilisateurs.</p>
            <a href="{{ url('/admin/users') }}" class="inline-block px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow hover:shadow-lg mt-2">
                Accéder à la gestion des utilisateurs
            </a>
        </div>
    </div>
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-8 md:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Créer une nouvelle Unité
                </h1>
                <p class="mt-3 text-gray-600">Remplissez les informations ci-dessous pour ajouter une nouvelle unité de mesure.</p>
            </div>

            <form action="{{ route('unites.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom de l'unité <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Nom de l'unité (ex: Kilogramme, Litre)"></i></label>
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
                        <label for="symbole" class="block text-sm font-medium text-gray-700">Symbole <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Symbole de l'unité (ex: kg, L)"></i></label>
                        <input type="text" 
                               name="symbole" 
                               id="symbole" 
                               value="{{ old('symbole') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm"
                               required>
                        @error('symbole')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Description facultative de l'unité"></i></label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="actif" class="block text-sm font-medium text-gray-700">Statut <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Indique si l'unité est active ou inactive"></i></label>
                    <select name="actif" 
                            id="actif"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 shadow-sm">
                        <option value="1" {{ old('actif', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('actif') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('actif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    
                    <a href="{{ route('unites.index') }}" 
                       class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Créer l'unité
                    </button>
                </div>
            </form>
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
