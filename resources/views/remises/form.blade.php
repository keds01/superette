<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($remise) ? __('Modifier la Remise') : __('Nouvelle Remise') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ isset($remise) ? route('remises.update', $remise) : route('remises.store') }}" method="POST" class="space-y-6">
                        @csrf
                        @if(isset($remise))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code_remise" class="block text-sm font-medium text-gray-700">Code de remise</label>
                                <input type="text" name="code_remise" id="code_remise" value="{{ old('code_remise', $remise->code_remise ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('code_remise')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type_remise" class="block text-sm font-medium text-gray-700">Type de remise</label>
                                <select name="type_remise" id="type_remise"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                    <option value="pourcentage" {{ (old('type_remise', $remise->type_remise ?? '') === 'pourcentage') ? 'selected' : '' }}>
                                        Pourcentage
                                    </option>
                                    <option value="montant_fixe" {{ (old('type_remise', $remise->type_remise ?? '') === 'montant_fixe') ? 'selected' : '' }}>
                                        Montant Fixe
                                    </option>
                                </select>
                                @error('type_remise')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Valeur -->
                            <div>
                                <label for="valeur_remise" class="block text-sm font-medium text-gray-700">
                                    Valeur <span id="valeurLabel">(en %)</span>
                                </label>
                                <input type="number" name="valeur_remise" id="valeur_remise" step="0.01" min="0"
                                    value="{{ old('valeur_remise', $remise->valeur_remise ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                @error('valeur_remise')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Utilisation maximale -->
                            <div>
                                <label for="utilisation_max" class="block text-sm font-medium text-gray-700">
                                    Nombre d'utilisations maximum (optionnel)
                                </label>
                                <input type="number" name="utilisation_max" id="utilisation_max" min="1"
                                    value="{{ old('utilisation_max', $remise->utilisation_max ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Laissez vide pour un nombre illimité d'utilisations</p>
                                @error('utilisation_max')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date de début -->
                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                                <input type="date" name="date_debut" id="date_debut"
                                    value="{{ old('date_debut', isset($remise) ? $remise->date_debut->format('Y-m-d') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                @error('date_debut')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date de fin -->
                            <div>
                                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                                <input type="date" name="date_fin" id="date_fin"
                                    value="{{ old('date_fin', isset($remise) ? $remise->date_fin->format('Y-m-d') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                @error('date_fin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if(isset($remise))
                            <!-- Statut (uniquement pour l'édition) -->
                            <div class="flex items-center">
                                <input type="checkbox" name="actif" id="actif" value="1"
                                    {{ old('actif', $remise->actif) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <label for="actif" class="ml-2 block text-sm text-gray-700">
                                    Actif
                                </label>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('remises.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                {{ isset($remise) ? 'Mettre à jour' : 'Créer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Mise à jour du label de la valeur selon le type
        document.getElementById('type_remise').addEventListener('change', function() {
            const valeurLabel = document.getElementById('valeurLabel');
            const valeurInput = document.getElementById('valeur_remise');
            
            if (this.value === 'pourcentage') {
                valeurLabel.textContent = '(en %)';
                valeurInput.step = '0.01';
                valeurInput.max = '100';
            } else {
                valeurLabel.textContent = '(en FCFA)';
                valeurInput.step = '1';
                valeurInput.max = '';
            }
        });

        // Déclencher l'événement au chargement
        document.getElementById('type_remise').dispatchEvent(new Event('change'));
    </script>
    @endpush
</x-app-layout> 