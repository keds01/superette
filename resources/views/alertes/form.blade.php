<form action="{{ isset($alerte) ? route('alertes.update', $alerte->id) : route('alertes.store') }}" method="POST" class="space-y-6">
    @csrf
    @if(isset($alerte))
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sélection du produit -->
        <div>
            <label for="produit_id" class="block text-sm font-medium text-indigo-700">Produit *</label>
            <select name="produit_id" id="produit_id" required
                    class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                <option value="">Sélectionner un produit</option>
                @foreach($produits as $produit)
                    <option value="{{ $produit->id }}" {{ (isset($alerte) && $alerte->produit_id == $produit->id) || old('produit_id') == $produit->id ? 'selected' : '' }}>
                        {{ $produit->nom }} ({{ $produit->categorie?->nom ?? 'Sans catégorie' }}) - Stock: {{ $produit->stock }}
                    </option>
                @endforeach
            </select>
            @error('produit_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Type d'alerte -->
        <div>
            <label for="type" class="block text-sm font-medium text-indigo-700">Type d'alerte *</label>
            <select name="type" id="type" required
                    class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500"
                    onchange="toggleAlerteFields()">
                <option value="seuil_minimum" {{ (isset($alerte) && $alerte->type == 'seuil_minimum') || old('type') == 'seuil_minimum' ? 'selected' : '' }}>
                    Seuil Minimum
                </option>
                <option value="seuil_maximum" {{ (isset($alerte) && $alerte->type == 'seuil_maximum') || old('type') == 'seuil_maximum' ? 'selected' : '' }}>
                    Seuil Maximum
                </option>
                <option value="peremption" {{ (isset($alerte) && $alerte->type == 'peremption') || old('type') == 'peremption' ? 'selected' : '' }}>
                    Date de Péremption
                </option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Seuil -->
        <div id="seuil-field">
            <label for="seuil" class="block text-sm font-medium text-indigo-700">
                Seuil <span class="text-gray-500 text-xs">(quantité)</span> *
            </label>
            <input type="number" name="seuil" id="seuil" min="0" step="0.01"
                   value="{{ old('seuil', $alerte->seuil ?? '') }}"
                   class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
            @error('seuil')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Période (pour alerte de péremption) -->
        <div id="periode-field" class="hidden">
            <label for="periode" class="block text-sm font-medium text-indigo-700">
                Période d'alerte <span class="text-gray-500 text-xs">(jours avant péremption)</span> *
            </label>
            <input type="number" name="periode" id="periode" min="1" step="1"
                   value="{{ old('periode', $alerte->periode ?? 15) }}"
                   class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
            @error('periode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-600">Ex: 15 jours signifie que l'alerte sera déclenchée 15 jours avant la date de péremption.</p>
        </div>

        <!-- Message (optionnel) -->
        <div>
            <label for="message" class="block text-sm font-medium text-indigo-700">
                Message personnalisé <span class="text-gray-500 text-xs">(optionnel)</span>
            </label>
            <input type="text" name="message" id="message"
                   value="{{ old('message', $alerte->message ?? '') }}"
                   class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500"
                   placeholder="Message qui s'affichera avec l'alerte">
            @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actif/Inactif -->
        @if(isset($alerte))
        <div class="flex items-center">
            <input type="checkbox" name="active" id="active" value="1"
                   {{ old('active', $alerte->active) ? 'checked' : '' }}
                   class="rounded border-indigo-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <label for="active" class="ml-2 block text-sm text-gray-700">
                Alerte active
            </label>
        </div>
        @endif
    </div>

    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('alertes.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl transition-colors">
            Annuler
        </a>
        <button type="submit" class="bg-gradient-to-tr from-indigo-600 to-purple-500 text-white px-6 py-3 rounded-xl font-bold shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-200">
            {{ isset($alerte) ? 'Mettre à jour' : 'Créer l\'alerte' }}
        </button>
    </div>
</form>
