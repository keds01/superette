@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-indigo-900">Modifier l'alerte ({{ $alertTypes[$alert->type] }})</h2>
                <p class="mt-2 text-gray-600">Modifiez les paramètres de votre alerte existante.</p>
            </div>

            <form action="{{ route('alertes.update', $alert) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Le type et la catégorie ne sont généralement pas modifiables après création --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type d'alerte</label>
                         <input type="text" id="type" value="{{ $alertTypes[$alert->type] }}" class="mt-1 block w-full rounded-xl border-indigo-200 bg-gray-100 cursor-not-allowed" readonly>
                         <input type="hidden" name="type" value="{{ $alert->type }}"> {{-- Champ caché pour la validation --}}
                    </div>
                    <div>
                        <label for="categorie_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                         <input type="text" id="categorie_id" value="{{ $alert->categorie ? $alert->categorie->nom : 'Toutes les catégories' }}" class="mt-1 block w-full rounded-xl border-indigo-200 bg-gray-100 cursor-not-allowed" readonly>
                         <input type="hidden" name="categorie_id" value="{{ $alert->categorie_id }}"> {{-- Champ caché pour la validation --}}
                    </div>
                </div>

                <div>
                    <label for="seuil" class="block text-sm font-medium text-gray-700">Seuil</label>
                    <input type="number" name="seuil" id="seuil" step="0.01" min="0" value="{{ old('seuil', $alert->seuil) }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    @error('seuil')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="periodeField" class="{{ !in_array($alert->type, ['peremption', 'mouvement_important']) ? 'hidden' : '' }}">
                    <label for="periode" class="block text-sm font-medium text-gray-700">Période (en jours)</label>
                    <input type="number" name="periode" id="periode" min="1" value="{{ old('periode', $alert->periode) }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                     @error('periode')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 <div>
                    <label for="notification_email" class="block text-sm font-medium text-gray-700">Email de notification (optionnel)</label>
                    <input type="email" name="notification_email" id="notification_email" value="{{ old('notification_email', $alert->notification_email) }}" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                     @error('notification_email')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="actif" id="actif" value="1" {{ old('actif', $alert->actif) ? 'checked' : '' }} class="h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="actif" class="ml-2 block text-sm text-gray-900">
                        Alerte active
                    </label>
                     @error('actif')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 <!-- Informations supplémentaires -->
                <div class="bg-indigo-50 rounded-xl p-4">
                    <h3 class="text-sm font-medium text-indigo-900 mb-2">Informations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p><span class="font-medium">Créée le :</span> {{ $alert->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p><span class="font-medium">Dernière modification :</span> {{ $alert->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <a href="{{ route('alertes.index') }}" 
                       class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeInput = document.getElementById('type'); // Input type text readonly
        const periodeField = document.getElementById('periodeField');
        const periodeInput = document.getElementById('periode');

        // La logique d'affichage/masquage de periode est basée sur le type non modifiable
        function togglePeriodeField() {
            const alertType = document.querySelector('input[name="type"]').value; // Récupérer la valeur du champ caché
            if (alertType === 'peremption' || alertType === 'mouvement_important') {
                periodeField.classList.remove('hidden');
                // periodeInput.required = true; // La validation est gérée par le controller required_if
            } else {
                periodeField.classList.add('hidden');
                periodeInput.value = ''; // Clear value when hidden
                // periodeInput.required = false;
            }
        }

        // Déclencher au chargement
        togglePeriodeField();
    });
</script>
@endpush
@endsection 