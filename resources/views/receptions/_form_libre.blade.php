<!-- Formulaire de réception libre (mode legacy, sans commande liée) -->
<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        Produits reçus
    </h3>
    <div id="produits-container" class="space-y-4">
        <!-- Template pour un produit -->
        <div class="produit-item bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                    <select name="produits[0][produit_id]" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}">{{ $produit->nom }} ({{ $produit->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" name="produits[0][quantite]" required min="0" step="0.01"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire</label>
                    <input type="number" name="produits[0][prix_unitaire]" required min="0" step="0.01"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de péremption</label>
                    <input type="date" name="produits[0][date_peremption]"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </div>
    <button type="button" id="ajouter-produit" 
            class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Ajouter un produit
    </button>
</div>
<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mt-6">
    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <textarea name="description" id="description" rows="3"
              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Décrivez les détails de cette réception...">{{ old('description') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('produits-container');
        const ajouterBtn = document.getElementById('ajouter-produit');
        let produitCount = container.querySelectorAll('.produit-item').length;

        ajouterBtn.addEventListener('click', function() {
            const template = container.querySelector('.produit-item').cloneNode(true);
            // Mettre à jour les noms des champs
            template.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${produitCount}]`);
                input.value = '';
            });
            container.appendChild(template);
            produitCount++;
        });
    });
</script>
@endpush
