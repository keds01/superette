@extends('layouts.app')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la Promotion</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Modifiez les détails de la promotion pour le produit {{ $promotion->product->nom }}.
                </p>
            </div>
            <a href="{{ route('promotions.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>

        <form action="{{ route('promotions.update', $promotion) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produit -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Produit</label>
                    <select name="product_id" id="product_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                {{ (old('product_id', $promotion->product_id) == $product->id) ? 'selected' : '' }}>
                                {{ $product->nom }} ({{ $product->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type de promotion -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type de promotion</label>
                    <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pourcentage" {{ old('type', $promotion->type) == 'pourcentage' ? 'selected' : '' }}>
                            Pourcentage
                        </option>
                        <option value="montant" {{ old('type', $promotion->type) == 'montant' ? 'selected' : '' }}>
                            Montant fixe
                        </option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valeur -->
                <div>
                    <label for="valeur" class="block text-sm font-medium text-gray-700">Valeur</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="valeur" id="valeur" step="0.01" min="0" required
                               value="{{ old('valeur', $promotion->valeur) }}"
                               class="block w-full rounded-md border-gray-300 pr-12 focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="valeur-suffix">
                                {{ $promotion->type === 'pourcentage' ? '%' : 'FCFA' }}
                            </span>
                        </div>
                    </div>
                    @error('valeur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de début -->
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="datetime-local" name="date_debut" id="date_debut" required
                           value="{{ old('date_debut', $promotion->date_debut->format('Y-m-d\TH:i')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date_debut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de fin -->
                <div>
                    <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="datetime-local" name="date_fin" id="date_fin" required
                           value="{{ old('date_fin', $promotion->date_fin->format('Y-m-d\TH:i')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('date_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="actif" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="actif" id="actif" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" {{ old('actif', $promotion->actif) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('actif', $promotion->actif) ? '' : 'selected' }}>Inactive</option>
                    </select>
                    @error('actif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $promotion->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('promotions.index') }}" 
                   class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-500 transition">
                    Mettre à jour la promotion
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Mise à jour du suffixe de la valeur selon le type
    document.getElementById('type').addEventListener('change', function() {
        const suffix = this.value === 'pourcentage' ? '%' : 'FCFA';
        document.getElementById('valeur-suffix').textContent = suffix;
    });

    // Validation des dates
    document.getElementById('date_debut').addEventListener('change', function() {
        const dateFin = document.getElementById('date_fin');
        if (dateFin.value && this.value > dateFin.value) {
            dateFin.value = this.value;
        }
        dateFin.min = this.value;
    });

    document.getElementById('date_fin').addEventListener('change', function() {
        const dateDebut = document.getElementById('date_debut');
        if (this.value < dateDebut.value) {
            this.value = dateDebut.value;
        }
    });
</script>
@endpush
@endsection 