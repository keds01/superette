@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-indigo-900">Créer une nouvelle alerte</h2>
                <p class="mt-2 text-gray-600">Configurez les paramètres de votre alerte.</p>
            </div>

            <form action="{{ route('alertes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type d'alerte</label>
                    <select name="type" id="type" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">-- Sélectionner un type --</option>
                        @foreach($alertTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categorie_id" class="block text-sm font-medium text-gray-700">Catégorie (optionnel)</label>
                    <select name="categorie_id" id="categorie_id" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seuil" class="block text-sm font-medium text-gray-700">Seuil</label>
                    <input type="number" name="seuil" id="seuil" step="0.01" min="0" value="{{ old('seuil') }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    @error('seuil')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="periodeField" class="{{ !in_array(old('type'), ['peremption', 'mouvement_important']) ? 'hidden' : '' }}">
                    <label for="periode" class="block text-sm font-medium text-gray-700">Période (en jours)</label>
                    <input type="number" name="periode" id="periode" min="1" value="{{ old('periode') }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                     @error('periode')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 <div>
                    <label for="notification_email" class="block text-sm font-medium text-gray-700">Email de notification (optionnel)</label>
                    <input type="email" name="notification_email" id="notification_email" value="{{ old('notification_email') }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                     @error('notification_email')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="actif" value="1" {{ old('actif', 1) ? 'checked' : '' }} class="h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="actif" class="ml-2 block text-sm text-gray-900">
                        Alerte active
                    </label>
                     @error('actif')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <a href="{{ route('alertes.index') }}" 
                       class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        Créer l'alerte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const periodeField = document.getElementById('periodeField');
        const periodeInput = document.getElementById('periode');

        function togglePeriodeField() {
            if (typeSelect.value === 'peremption' || typeSelect.value === 'mouvement_important') {
                periodeField.classList.remove('hidden');
                periodeInput.required = true;
            } else {
                periodeField.classList.add('hidden');
                periodeInput.required = false;
                 periodeInput.value = ''; // Clear value when hidden
            }
        }

        typeSelect.addEventListener('change', togglePeriodeField);

        // Déclencher au chargement pour gérer l'ancien input si validation échoue
        togglePeriodeField();
    });
</script>
@endpush
@endsection 