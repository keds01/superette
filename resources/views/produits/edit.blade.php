{{-- Placeholder for edit view --}}

@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header moderne -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
                <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Modifier le Produit</h1>
                <p class="mt-2 text-lg text-gray-500">Mettez à jour les informations du produit "{{ $produit->nom }}".</p>
                </div>
                <a href="{{ route('produits.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
            <!-- Info box contextuelle -->
            <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-6 rounded-xl shadow flex gap-4 items-start animate-fade-in">
                <span class="ml-2 relative group align-middle">
                    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
                    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
                        Indique l'unité de base pour le stock (ex : Pièce, KG…)
                        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
                    </span>
                </span>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-blue-700 mb-1">Conseils pour la modification d'un produit</h2>
                    <ul class="list-disc list-inside text-blue-800 text-sm space-y-1">
                        <li><span class="font-semibold">Champs obligatoires :</span> Les champs marqués d'une <span class="text-red-500">*</span> doivent être remplis (nom, catégorie, prix, stock…)</li>
                        <li><span class="font-semibold">Nommage :</span> Utilisez un nom clair et unique pour chaque produit. Évitez les doublons et abréviations ambiguës.</li>
                        <li><span class="font-semibold">Catégorisation :</span> Vérifiez que la catégorie sélectionnée correspond bien au type de produit (ex : Boissons, Épicerie, Frais…)</li>
                        <li><span class="font-semibold">Image :</span> Ajoutez une image nette et représentative pour faciliter l'identification en caisse.</li>
                    </ul>
                </div>
            </div>
        <!-- Formulaire principal -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <div>
                            <p class="font-semibold mb-2">Veuillez corriger les erreurs ci-dessous&nbsp;:</p>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                                        </div>
                                    </div>
                                </div>
            @endif
            <form action="{{ route('produits.update', $produit->id) }}" method="POST" enctype="multipart/form-data" id="productForm" class="space-y-8">
                @csrf
                @method('PUT')
                <!-- Section 1: Informations Générales & Image -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Colonne Informations Générales -->
                    <div class="lg:col-span-2 bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-6">
                            <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-tag text-indigo-500"></i> Informations Générales
                            </h3>
                            <div class="mb-6 p-4 bg-amber-50/70 rounded-lg border border-amber-100">
                                <p class="text-amber-800 text-sm">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Ces informations sont essentielles pour l'identification et la recherche du produit dans le système. Choisissez un nom clair et une référence unique.
                                </p>
                            </div>
                                <div>
                                <label for="nom" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                    Nom du produit <span class="text-red-500">*</span>
                                    <span class="ml-1 bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full">Affiché en caisse</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-box text-indigo-400"></i></div>
                                    <input type="text" id="nom" name="nom" value="{{ old('nom', $produit->nom) }}" placeholder="Ex: Coca-Cola 1.5L" required class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Nom commercial complet avec format/taille si applicable</p>
                                @error('nom') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                <div>
                                    <label for="code_barres" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                        Code-barres <span class="text-red-500">*</span>
                                        <span class="ml-1 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Pour scanner</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-barcode text-indigo-400"></i></div>
                                        <input type="text" id="code_barres" name="code_barres" value="{{ old('code_barres', $produit->code_barres) }}" placeholder="Ex: 5410123456789" required class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">EAN-13, UPC ou autre code standard (obligatoire)</p>
                                    @error('code_barres') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <input type="hidden" id="reference" name="reference" value="{{ old('reference', $produit->reference) }}">
                                <input type="hidden" id="reference_interne" name="reference_interne" value="{{ old('reference_interne', $produit->reference_interne ?? '') }}">
                                </div>
                                <div>
                                <label for="categorie_id" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                    Catégorie <span class="text-red-500">*</span>
                                    <span class="ml-1 bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full">Pour classement</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-tags text-indigo-400"></i></div>
                                    <select id="categorie_id" name="categorie_id" required class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                                        <option value="">Sélectionner une catégorie</option>
                                            @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ old('categorie_id', $produit->categorie_id) == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                <p class="mt-1 text-xs text-gray-500">Groupe de produits pour le classement et les rapports</p>
                                @error('categorie_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <input type="hidden" id="description" name="description" value="{{ old('description', $produit->description) }}">
                        </div>
                    <!-- Colonne Image -->
                    <div class="lg:col-span-1 bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-4">
                            <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-image text-indigo-500"></i> Image du Produit
                            </h3>
                        <div class="flex flex-col items-center">
                                <div id="imagePreviewContainer" class="w-full h-64 mb-4 border-2 border-dashed border-indigo-300 rounded-lg flex items-center justify-center bg-indigo-50/50 overflow-hidden">
                                    <img id="imagePreview" src="{{ $produit->image_url ?? asset('assets/img/placeholder-image.png') }}" alt="Aperçu de l'image" class="max-h-full max-w-full object-contain {{ $produit->image ? '' : 'hidden' }}">
                                    <span id="imagePreviewPlaceholder" class="{{ $produit->image ? 'hidden' : 'text-indigo-400 text-center p-4' }}">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i><br>
                                        Cliquez ou glissez-déposez une image
                                    </span>
                                </div>
                                <input type="file" name="image" id="image" class="hidden" accept="image/*" onchange="previewImage(this);">
                                <label for="image" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-tr from-blue-500 to-teal-400 text-white rounded-lg shadow-md hover:shadow-neon hover:-translate-y-1 transition-all">
                                    <i class="fas fa-upload"></i> Choisir une image
                                </label>
                                <span class="ml-2 relative group align-middle">
                                    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
                                    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
                                        Formats acceptés : JPG, PNG. Image nette pour faciliter l'identification en rayon.
                                        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
                                    </span>
                                </span>
                                @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                <!-- Section 2: Détails & Spécifications -->
                <div class="bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-box-open text-indigo-500"></i> Détails & Spécifications
                        </h3>
                        <div class="mb-6 p-4 bg-blue-50/70 rounded-lg border border-blue-100">
                            <p class="text-blue-800 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Cette section définit comment le produit est vendu et stocké. Les unités déterminent comment le produit apparaîtra en caisse et dans l'inventaire.
                            </p>
                        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Unité de vente -->
                                <div>
                                <label for="unite_vente_id" class="block text-sm font-medium text-indigo-700 mb-1">
                                    <span class="flex items-center">
                                        Unité de vente <span class="text-red-500 ml-1">*</span>
                                        <span class="ml-1 bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full">En caisse</span>
                                    </span>
                                </label>
                                <select name="unite_vente_id" id="unite_vente_id" required class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                                    <option value="">Sélectionner une unité de vente</option>
                                    @foreach($unites_vente as $unite_vente)
                                        <option value="{{ $unite_vente->id }}" {{ old('unite_vente_id', $produit->unite_vente_id) == $unite_vente->id ? 'selected' : '' }}>{{ $unite_vente->nom }}</option>
                                            @endforeach
                                        </select>
                                @error('unite_vente_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    </div>
                                </div>
                <!-- Section 3: Prix et Conditionnements -->
                <div class="bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-6">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-cubes text-indigo-500"></i> Prix et Conditionnements
                        </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-indigo-50/70 rounded-lg">
                                <div>
                            <label for="prix_achat_ht" class="block text-sm font-medium">Prix d'achat HT <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-indigo-400"></i>
                                </div>
                                <input type="number" id="prix_achat_ht" name="prix_achat_ht" value="{{ old('prix_achat_ht', $produit->prix_achat_ht) }}" required step="0.01" class="block w-full rounded-lg border">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Prix auquel vous achetez le produit (hors taxes)</p>
                                @error('prix_achat_ht') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                            <label for="prix_vente_ttc" class="block text-sm font-medium">Prix de vente TTC <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-tag text-indigo-400"></i>
                                </div>
                                <input type="number" id="prix_vente_ttc" name="prix_vente_ttc" value="{{ old('prix_vente_ttc', $produit->prix_vente_ttc) }}" required step="0.01" class="block w-full rounded-lg border">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Prix de vente pour une unité (taxes comprises)</p>
                                @error('prix_vente_ttc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    <h4 class="text-md font-medium text-indigo-800">Tarifs par conditionnement</h4>
                    <div id="conditionnements-list" class="space-y-3">
                        @php
                            $conditionnements = old('conditionnements', $produit->conditionnements->toArray());
                        @endphp
                        @forelse($conditionnements as $i => $cond)
                            <div class="grid grid-cols-4 gap-4 items-center conditionnement-row">
                                <input type="hidden" name="conditionnements[{{ $i }}][id]" value="{{ $cond['id'] ?? '' }}">
                                <input type="text" name="conditionnements[{{ $i }}][type]" value="{{ $cond['type'] }}" placeholder="Type (ex: Pack de 6)" required class="block w-full rounded-lg border">
                                <input type="number" name="conditionnements[{{ $i }}][quantite]" value="{{ $cond['quantite'] }}" placeholder="Quantité" min="1" required class="block w-full rounded-lg border">
                                <input type="number" name="conditionnements[{{ $i }}][prix]" value="{{ $cond['prix'] }}" placeholder="Prix" min="0" step="0.01" required class="block w-full rounded-lg border">
                                <button type="button" class="remove-cond bg-red-500 text-white p-2 rounded-lg">X</button>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <button type="button" id="add-cond" class="bg-blue-500 text-white p-2 rounded-lg">+ Ajouter un tarif</button>
                                </div>
                <!-- Section 4: Stock & Péremption -->
                <div class="bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-warehouse text-indigo-500"></i> Stock & Péremption
                        </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="stock" class="block text-sm font-medium">Stock actuel</label>
                            <input type="number" name="stock" value="{{ old('stock', $produit->stock) }}" class="block w-full rounded-lg border">
                                            </div>
                        <div>
                            <label for="seuil_alerte" class="block text-sm font-medium">Seuil d'alerte <span class="text-red-500">*</span></label>
                            <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" required class="block w-full rounded-lg border">
                                    </div>
                        <div>
                            <label for="date_peremption" class="block text-sm font-medium">Date de péremption</label>
                            <input type="date" name="date_peremption" value="{{ old('date_peremption', $produit->date_peremption ? $produit->date_peremption->format('Y-m-d') : '') }}" class="block w-full rounded-lg border">
                            </div>
                        </div>
                    </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-green-500 text-white font-bold text-lg"><i class="fas fa-save"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

    @push('scripts')
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Image Preview
    window.previewImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('imagePreviewPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
    // Ajouter/Supprimer Conditionnements
    let condIndex = {{ isset($conditionnements) ? count($conditionnements) : 0 }};
    document.getElementById('add-cond').addEventListener('click', function() {
        const list = document.getElementById('conditionnements-list');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-4 gap-4 items-center conditionnement-row';
        row.innerHTML = `
            <input type="hidden" name="conditionnements[${condIndex}][id]" value="">
            <input type="text" name="conditionnements[${condIndex}][type]" placeholder="Type (ex: Pack de 6)" required class="block w-full rounded-lg border">
            <input type="number" name="conditionnements[${condIndex}][quantite]" placeholder="Quantité" min="1" required class="block w-full rounded-lg border">
            <input type="number" name="conditionnements[${condIndex}][prix]" placeholder="Prix" min="0" step="0.01" required class="block w-full rounded-lg border">
            <button type="button" class="remove-cond bg-red-500 text-white p-2 rounded-lg">X</button>
        `;
        list.appendChild(row);
        condIndex++;
    });
    document.getElementById('conditionnements-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-cond')) {
            e.target.closest('.conditionnement-row').remove();
        }
    });
    });
    </script>
    @endpush