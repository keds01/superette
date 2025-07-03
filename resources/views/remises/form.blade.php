{{-- Pas de layout ici, juste le formulaire --}}
@if(isset($vente))
    <div class="mb-6 p-4 bg-indigo-50 border-l-4 border-indigo-400 rounded">
        <div class="font-semibold text-indigo-700 mb-1">Vente sélectionnée :</div>
        <div class="text-sm text-gray-700">
            <span class="font-bold">#{{ $vente->id }}</span> —
            {{ $vente->created_at->format('d/m/Y H:i') }} —
            {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA
            @if($vente->client)
                <div class="mt-1">Client: <span class="font-medium">{{ $vente->client->nom }} {{ $vente->client->prenom }}</span></div>
            @endif
        </div>
    </div>
@endif
<form action="{{ isset($vente) ? route('remises.store', ['vente' => $vente->id]) : '' }}" method="POST" class="space-y-6">
    @csrf
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
            
            @if(isset($vente))
                <div class="mt-2 text-sm text-gray-500" id="previewRemise">
                    @if(isset($vente))
                        <div class="font-medium">Montant total actuel: {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</div>
                    @endif
                </div>
            @endif
        </div>
        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">
                Description (optionnelle)
            </label>
            <textarea name="description" id="description" rows="2" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $remise->description ?? '') }}</textarea>
            @error('description')
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
            {{ isset($remise) ? 'Mettre à jour' : 'Créer la remise' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mise à jour du label de la valeur selon le type
        const updatePreview = function() {
            const typeRemise = document.getElementById('type_remise');
            const valeurInput = document.getElementById('valeur_remise');
            const valeurLabel = document.getElementById('valeurLabel');
            const previewDiv = document.getElementById('previewRemise');
            
            if (!previewDiv) return;
            
            @if(isset($vente))
            const montantTotal = {{ $vente->montant_total }};
            let remise = 0;
            
            if (typeRemise.value === 'pourcentage') {
                valeurLabel.textContent = '(en %)';
                valeurInput.step = '0.01';
                valeurInput.max = '100';
                
                if (valeurInput.value) {
                    remise = (montantTotal * parseFloat(valeurInput.value)) / 100;
                }
            } else {
                valeurLabel.textContent = '(en FCFA)';
                valeurInput.step = '1';
                valeurInput.max = montantTotal;
                
                if (valeurInput.value) {
                    remise = Math.min(parseFloat(valeurInput.value), montantTotal);
                }
            }
            
            const montantFinal = montantTotal - remise;
            
            previewDiv.innerHTML = `
                <div class="font-medium">Montant total actuel: ${new Intl.NumberFormat('fr-FR').format(montantTotal)} FCFA</div>
                <div class="text-indigo-600">Remise calculée: ${new Intl.NumberFormat('fr-FR').format(remise)} FCFA</div>
                <div class="font-bold text-green-600">Montant après remise: ${new Intl.NumberFormat('fr-FR').format(montantFinal)} FCFA</div>
            `;
            @endif
        };
        
        const typeRemise = document.getElementById('type_remise');
        const valeurInput = document.getElementById('valeur_remise');
        
        if (typeRemise && valeurInput) {
            typeRemise.addEventListener('change', updatePreview);
            valeurInput.addEventListener('input', updatePreview);
            
            // Déclencher l'événement au chargement
            updatePreview();
        }
    });
</script>
@endpush 