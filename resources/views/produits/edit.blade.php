@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header moderne -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
                <div>
                    <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Modifier le produit</h1>
                    <p class="mt-2 text-lg text-gray-500">Modifiez les informations du produit étape par étape</p>
                </div>
                <a href="{{ route('produits.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            <!-- Formulaire avec design moderne -->
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
                <form action="{{ route('produits.update', $produit) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="productForm">
                    @csrf
                    @method('PUT')

                    <!-- Informations de base -->
                    <div class="mb-8">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <h5 class="text-lg font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white">
                                    <i class="fas fa-tag"></i>
                                </div>
                                Informations de base
                            </h5>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Nom du produit <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-box text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="nom" 
                                               name="nom" 
                                               value="{{ old('nom', $produit->nom) }}" 
                                               required>
                                    </div>
                                    @error('nom')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reference" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Référence <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-barcode text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="reference" 
                                               name="reference" 
                                               value="{{ old('reference', $produit->reference) }}" 
                                               required>
                                    </div>
                                    @error('reference')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code_barres" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Code-barres
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-qrcode text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="code_barres" 
                                               name="code_barres" 
                                               value="{{ old('code_barres', $produit->code_barres) }}">
                                    </div>
                                    @error('code_barres')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="categorie_id" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Catégorie <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-tags text-indigo-400"></i>
                                        </div>
                                        <select class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                                id="categorie_id" 
                                                name="categorie_id" 
                                                required>
                                            <option value="">Sélectionner une catégorie</option>
                                            @foreach($categories as $categorie)
                                                <option value="{{ $categorie->id }}" {{ old('categorie_id', $produit->categorie_id) == $categorie->id ? 'selected' : '' }}>
                                                    {{ $categorie->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('categorie_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="description" class="block text-sm font-medium text-indigo-700 mb-1">
                                    Description
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 text-indigo-400">
                                        <i class="fas fa-align-left"></i>
                                    </div>
                                    <textarea class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description', $produit->description) }}</textarea>
                                </div>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Conditionnement -->
                    <div class="mb-8">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <h5 class="text-lg font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white">
                                    <i class="fas fa-box"></i>
                                </div>
                                Conditionnement
                            </h5>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="unite_vente_id" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Unité de vente <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-ruler text-indigo-400"></i>
                                        </div>
                                        <select class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                                id="unite_vente_id" 
                                                name="unite_vente_id" 
                                                required>
                                            <option value="">Sélectionner une unité</option>
                                            @foreach($units as $unite)
                                                <option value="{{ $unite->id }}" {{ old('unite_vente_id', $produit->unite_vente_id) == $unite->id ? 'selected' : '' }}>
                                                    {{ $unite->nom }} ({{ $unite->symbole }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('unite_vente_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="conditionnement_fournisseur" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Conditionnement fournisseur <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-boxes text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="conditionnement_fournisseur" 
                                               name="conditionnement_fournisseur" 
                                               value="{{ old('conditionnement_fournisseur', $produit->conditionnement_fournisseur) }}" 
                                               required>
                                    </div>
                                    @error('conditionnement_fournisseur')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="quantite_par_conditionnement" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Quantité par conditionnement <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-sort-numeric-up text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="quantite_par_conditionnement" 
                                               name="quantite_par_conditionnement" 
                                               value="{{ old('quantite_par_conditionnement', $produit->quantite_par_conditionnement) }}" 
                                               required 
                                               min="0.01" 
                                               step="0.01">
                                    </div>
                                    @error('quantite_par_conditionnement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prix et TVA -->
                    <div class="mb-8">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <h5 class="text-lg font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                Prix et TVA
                            </h5>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="prix_achat_ht" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Prix d'achat HT <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-shopping-cart text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="prix_achat_ht" 
                                               name="prix_achat_ht" 
                                               value="{{ old('prix_achat_ht', $produit->prix_achat_ht) }}" 
                                               required 
                                               min="0" 
                                               step="0.01" 
                                               onchange="calculerPrixVente()">
                                    </div>
                                    @error('prix_achat_ht')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="marge" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Marge (%) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-percentage text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="marge" 
                                               name="marge" 
                                               value="{{ old('marge', $produit->marge) }}" 
                                               required 
                                               min="0" 
                                               max="100" 
                                               step="0.01" 
                                               onchange="calculerPrixVente()">
                                    </div>
                                    @error('marge')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tva" class="block text-sm font-medium text-indigo-700 mb-1">
                                        TVA (%) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-percent text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="tva" 
                                               name="tva" 
                                               value="{{ old('tva', $produit->tva) }}" 
                                               required 
                                               min="0" 
                                               max="100" 
                                               step="0.01" 
                                               onchange="calculerPrixVente()">
                                    </div>
                                    @error('tva')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="prix_vente_ttc" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Prix de vente TTC <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-tag text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all bg-gray-50" 
                                               id="prix_vente_ttc" 
                                               name="prix_vente_ttc" 
                                               value="{{ old('prix_vente_ttc', $produit->prix_vente_ttc) }}" 
                                               readonly>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Calculé automatiquement</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock et emplacement -->
                    <div class="mb-8">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <h5 class="text-lg font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                Stock et emplacement
                            </h5>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Stock actuel <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-boxes-stacked text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="stock" 
                                               name="stock" 
                                               value="{{ old('stock', $produit->stock) }}" 
                                               required 
                                               min="0" 
                                               step="0.01">
                                    </div>
                                    @error('stock')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="seuil_alerte" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Seuil d'alerte <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-bell text-indigo-400"></i>
                                        </div>
                                        <input type="number" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="seuil_alerte" 
                                               name="seuil_alerte" 
                                               value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" 
                                               required 
                                               min="0" 
                                               step="0.01">
                                    </div>
                                    @error('seuil_alerte')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emplacement_rayon" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Rayon <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-shelf text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="emplacement_rayon" 
                                               name="emplacement_rayon" 
                                               value="{{ old('emplacement_rayon', $produit->emplacement_rayon) }}" 
                                               required>
                                    </div>
                                    @error('emplacement_rayon')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emplacement_etagere" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Étagère <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-layer-group text-indigo-400"></i>
                                        </div>
                                        <input type="text" 
                                               class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                               id="emplacement_etagere" 
                                               name="emplacement_etagere" 
                                               value="{{ old('emplacement_etagere', $produit->emplacement_etagere) }}" 
                                               required>
                                    </div>
                                    @error('emplacement_etagere')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_peremption" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Date de péremption
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                        </div>
                                        <input 
                                               type="date" 
                                               id="date_peremption" 
                                               name="date_peremption" 
                                               value="{{ old('date_peremption', $produit->date_peremption ? $produit->date_peremption->format('Y-m-d') : null) }}" 
                                               class="block w-full pl-10 pr-3 py-2.5 rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm"
                                        >
                                    </div>
                                    @error('date_peremption')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="delai_alerte_peremption" class="block text-sm font-medium text-indigo-700 mb-1">
                                        Délai d'alerte avant péremption (jours)
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-bell text-gray-400"></i>
                                        </div>
                                        <input 
                                               type="number" 
                                               id="delai_alerte_peremption" 
                                               name="delai_alerte_peremption" 
                                               value="{{ old('delai_alerte_peremption', $produit->delai_alerte_peremption) }}" 
                                               min="1" 
                                               max="365" 
                                               placeholder="30" 
                                               class="block w-full pl-10 pr-12 py-2.5 rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-sm text-gray-400">jours</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Spécifiez combien de jours avant la péremption l'alerte doit être déclenchée (laisser vide = 30 jours par défaut)</p>
                                    @error('delai_alerte_peremption')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="mb-8">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <h5 class="text-lg font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white">
                                    <i class="fas fa-image"></i>
                                </div>
                                Image du produit
                            </h5>

                            <div class="image-upload-container">
                                <div class="image-preview-container rounded-xl border-2 border-dashed border-indigo-200 p-6 text-center transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50/50" id="imagePreviewContainer">
                                    <input type="file" 
                                           class="hidden" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*" 
                                           onchange="previewImage(this)">
                                    <div id="imagePreview" class="relative">
                                        @if($produit->image)
                                            <img src="{{ Storage::url($product->image) }}" 
                                                 alt="{{ $product->nom }}" 
                                                 class="mx-auto rounded-lg shadow-lg max-h-48">
                                            <button type="button" 
                                                    class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors duration-200"
                                                    onclick="removeImage()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <div class="upload-placeholder flex flex-col items-center justify-center min-h-[200px]">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-indigo-400 mb-3"></i>
                                                <p class="text-gray-500 mb-2">Glissez-déposez une image ici</p>
                                                <p class="text-gray-400 text-sm mb-3">ou</p>
                                                <button type="button" 
                                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-medium shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                                                        onclick="document.getElementById('image').click()">
                                                    <i class="fas fa-folder-open"></i>
                                                    Parcourir les fichiers
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500 text-center">
                                    Formats acceptés: JPG, PNG, GIF (max 2MB)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="flex justify-center mt-8">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-emerald-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-save"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Styles personnalisés pour les animations et transitions */
    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Effet de survol pour les cartes */
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Effet de survol pour les boutons */
    .shadow-neon {
        box-shadow: 0 0 15px rgba(79, 70, 229, 0.5);
    }

    /* Styles de l'upload d'image */
    .image-preview-container {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .image-preview-container.dragover {
        border-color: #4f46e5;
        background-color: rgba(79, 70, 229, 0.05);
    }

    /* Animation de chargement */
    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .loading::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 2rem;
        height: 2rem;
        margin: -1rem 0 0 -1rem;
        border: 3px solid #e5e7eb;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 1001;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Styles pour les étapes */
    .step {
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 80px;
    }

    .step span {
        position: static;
        transform: none;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .step {
            min-width: 50px;
        }
        
        .step span {
            font-size: 0.65rem;
            max-width: 60px;
        }
    }

    .step.active > div {
        background: linear-gradient(to top right, #4f46e5, #8b5cf6);
        border-color: transparent;
        color: white;
    }

    .step.active span {
        color: #4f46e5;
        font-weight: 600;
    }

    .step.completed > div {
        background: linear-gradient(to top right, #4f46e5, #8b5cf6);
        border-color: transparent;
        color: white;
    }

    .step-content {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animation de la barre de progression */
    #progressBar {
        transition: width 0.5s ease-in-out;
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    // Gestion des étapes
    let currentStep = 1;
    const totalSteps = 5;

    function updateStep(step) {
        // Mise à jour de la barre de progression
        const progress = ((step - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progressBar').style.width = `${progress}%`;

        // Mise à jour des indicateurs d'étape
        document.querySelectorAll('.step').forEach((el, index) => {
            if (index + 1 < step) {
                el.classList.add('completed');
                el.classList.remove('active');
            } else if (index + 1 === step) {
                el.classList.add('active');
                el.classList.remove('completed');
            } else {
                el.classList.remove('active', 'completed');
            }
        });

        // Affichage du contenu de l'étape
        document.querySelectorAll('.step-content').forEach((el, index) => {
            if (index + 1 === step) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });

        // Gestion des boutons de navigation
        const prevButton = document.getElementById('prevStep');
        const nextButton = document.getElementById('nextStep');
        const submitButton = document.getElementById('submitForm');

        if (step === 1) {
            prevButton.classList.add('hidden');
        } else {
            prevButton.classList.remove('hidden');
        }

        if (step === totalSteps) {
            nextButton.classList.add('hidden');
            submitButton.classList.remove('hidden');
        } else {
            nextButton.classList.remove('hidden');
            submitButton.classList.add('hidden');
        }

        currentStep = step;
    }

    // Navigation entre les étapes
    document.getElementById('nextStep').addEventListener('click', () => {
        if(currentStep < totalSteps) {
            updateStep(currentStep + 1);
        }
    });

    document.getElementById('prevStep').addEventListener('click', () => {
        if(currentStep > 1) {
            updateStep(currentStep - 1);
        }
    });

    // Validation des étapes
    function validateStep(step) {
        const currentStepContent = document.querySelector(`.step-content[data-step="${step}"]`);
        // Ajouter la validation si nécessaire
        return true;
    }

    // Ajout d'un gestionnaire d'événements après validation
    document.getElementById('nextStep').addEventListener('click', () => {
        if (validateStep(currentStep) && currentStep < totalSteps) {
            updateStep(currentStep + 1);
        }
    });

    // Calcul du prix de vente
    function calculerPrixVente() {
        const prixAchatHT = parseFloat(document.getElementById('prix_achat_ht').value) || 0;
        const marge = parseFloat(document.getElementById('marge').value) || 0;
        const tva = parseFloat(document.getElementById('tva').value) || 0;

        const prixVenteHT = prixAchatHT * (1 + marge / 100);
        const prixVenteTTC = prixVenteHT * (1 + tva / 100);

        document.getElementById('prix_vente_ttc').value = prixVenteTTC.toFixed(2);
    }

    // Gestion de l'image
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Vérification de la taille
            if (file.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Aperçu" 
                         class="mx-auto rounded-lg shadow-lg max-h-48">
                    <button type="button" 
                            class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors duration-200"
                            onclick="removeImage()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        const preview = document.getElementById('imagePreview');
        const input = document.getElementById('image');
        
        preview.innerHTML = `
            <div class="upload-placeholder flex flex-col items-center justify-center min-h-[200px]">
                <i class="fas fa-cloud-upload-alt text-4xl text-indigo-400 mb-3"></i>
                <p class="text-gray-500 mb-2">Glissez-déposez une image ici</p>
                <p class="text-gray-400 text-sm mb-3">ou</p>
                <button type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-medium shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                        onclick="document.getElementById('image').click()">
                    <i class="fas fa-folder-open"></i>
                    Parcourir les fichiers
                </button>
            </div>
        `;
        input.value = '';
    }

    // Gestion du drag & drop pour l'image
    const dropZone = document.getElementById('imagePreviewContainer');
    const fileInput = document.getElementById('image');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                fileInput.files = files;
                previewImage(fileInput);
            } else {
                alert('Format de fichier non supporté');
            }
        }
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        calculerPrixVente();
    });
    </script>
    @endpush
@endsection