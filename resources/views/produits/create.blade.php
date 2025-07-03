@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Nouveau Produit</h1>
                <p class="mt-2 text-lg text-gray-500">Créez un nouveau produit en renseignant les informations ci-dessous.</p>
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
        Indique l'unité de base pour le stock (ex : Pièce, KG…)
        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
    </span>
</span>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-blue-700 mb-1">Conseils pour la création d'un produit</h2>
                <ul class="list-disc list-inside text-blue-800 text-sm space-y-1">
                    <li><span class="font-semibold">Champs obligatoires :</span> Les champs marqués d'une <span class="text-red-500">*</span> doivent être remplis (nom, catégorie, prix, stock…)</li>
                    <li><span class="font-semibold">Nommage :</span> Utilisez un nom clair et unique pour chaque produit. Évitez les doublons et abréviations ambiguës.</li>
                    <li><span class="font-semibold">Catégorisation :</span> Vérifiez que la catégorie sélectionnée correspond bien au type de produit (ex : Boissons, Épicerie, Frais…)</li>
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
            <form action="{{ route('produits.store') }}" method="POST" enctype="multipart/form-data" id="productForm" class="space-y-8">
                @csrf

                <!-- Section 1: Informations Générales & Image -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Colonne Informations Générales -->
                    <div class="lg:col-span-2 bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-6">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-tag text-indigo-500"></i> Informations Générales
                        </h3>
                        
                        <!-- Note explicative -->
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
        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Ex: Coca-Cola 1.5L" required class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
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
                                    <input type="text" id="code_barres" name="code_barres" value="{{ old('code_barres') }}" placeholder="Ex: 5410123456789" required class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
</div>
                                <p class="mt-1 text-xs text-gray-500">EAN-13, UPC ou autre code standard (obligatoire)</p>
@error('code_barres') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <!-- Champs cachés pour la compatibilité -->
                            <input type="hidden" id="reference" name="reference" value="{{ old('reference', 'AUTO-'.time()) }}">
                            <input type="hidden" id="reference_interne" name="reference_interne" value="{{ old('reference_interne', '') }}">
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
            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
        @endforeach
    </select>
</div>
                            <p class="mt-1 text-xs text-gray-500">Groupe de produits pour le classement et les rapports</p>
@error('categorie_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Champs cachés pour la compatibilité -->
                        <input type="hidden" id="description" name="description" value="{{ old('description', '') }}">
                    </div>

                    <!-- Colonne Image -->
                    <div class="lg:col-span-1 bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-4">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-image text-indigo-500"></i> Image du Produit
                        </h3>
                        <div class="flex flex-col items-center">
                            <div id="imagePreviewContainer" class="w-full h-64 mb-4 border-2 border-dashed border-indigo-300 rounded-lg flex items-center justify-center bg-indigo-50/50 overflow-hidden">
    <img id="imagePreview" src="{{ asset('assets/img/placeholder-image.png') }}" alt="Aperçu de l'image" class="max-h-full max-w-full object-contain {{ old('image') ? '' : 'hidden' }}">
    <span id="imagePreviewPlaceholder" class="{{ old('image') ? 'hidden' : 'text-indigo-400 text-center p-4' }}">
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
        Formats acceptés : JPG, PNG. Image nette pour faciliter l'identification en rayon.
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
                    
                    <!-- Introduction explicative -->
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
                                    <option value="{{ $unite_vente->id }}" {{ old('unite_vente_id') == $unite_vente->id ? 'selected' : '' }}>{{ $unite_vente->nom }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Ex: Bouteille, Sachet, Pièce, Lot...</p>
                            @error('unite_vente_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Unité de mesure -->
                        <div>
                            <label for="unite_mesure" class="block text-sm font-medium text-indigo-700 mb-1">
                                <span class="flex items-center">
                                    Unité de mesure
                                    <span class="ml-1 bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full">Pour le stock</span>
    </span>
                            </label>
                            <select name="unite_mesure" id="unite_mesure" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                                <option value="Pièce" {{ old('unite_mesure') == 'Pièce' ? 'selected' : '' }}>Pièce</option>
                                <option value="KG" {{ old('unite_mesure') == 'KG' ? 'selected' : '' }}>Kilogramme (KG)</option>
                                <option value="Litre" {{ old('unite_mesure') == 'Litre' ? 'selected' : '' }}>Litre (L)</option>
                                <option value="Paquet" {{ old('unite_mesure') == 'Paquet' ? 'selected' : '' }}>Paquet</option>
                                <option value="Carton" {{ old('unite_mesure') == 'Carton' ? 'selected' : '' }}>Carton</option>
                                <option value="Autre" {{ old('unite_mesure') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Comment le produit est mesuré</p>
                        </div>
                        
                        <!-- Précision unité -->
                        <div id="valeur_mesure_container" class="{{ old('unite_mesure') == 'Autre' ? '' : 'hidden' }}">
                            <label for="valeur_mesure" class="block text-sm font-medium text-indigo-700 mb-1">Précisez l'unité</label>
                            <input type="text" name="valeur_mesure" id="valeur_mesure" value="{{ old('valeur_mesure') }}" placeholder="Ex: Bouteille de 75cl" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Si vous avez choisi "Autre"</p>
                            @error('valeur_mesure') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Champs cachés pour compatibilité -->
                        <input type="hidden" name="conditionnement_fournisseur" id="conditionnement_fournisseur" value="{{ old('conditionnement_fournisseur', 'Carton standard') }}">
                        <input type="hidden" name="quantite_par_conditionnement" id="quantite_par_conditionnement" value="{{ old('quantite_par_conditionnement', '1') }}">
                        <input type="hidden" name="poids" id="poids" value="{{ old('poids', 0) }}">
                        <input type="hidden" name="volume" id="volume" value="{{ old('volume', 0) }}">
                        <input type="hidden" name="fournisseur_id" id="fournisseur_id" value="{{ old('fournisseur_id', '') }}">
                        <input type="hidden" name="marque_id" id="marque_id" value="{{ old('marque_id', '') }}">
                    </div>
                </div>
                
                <!-- Section 3: Conditionnements -->
                <div class="bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6 space-y-6">
                        <h3 class="text-xl font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-cubes text-indigo-500"></i> Prix et Conditionnements
                        </h3>
                    
                    <!-- Prix de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-indigo-50/70 rounded-lg border border-indigo-100">
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
                                       value="{{ old('prix_achat_ht') }}"
                                       required
                                       min="0"
                                       step="0.01">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Prix auquel vous achetez le produit (hors taxes)</p>
                            @error('prix_achat_ht')
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
                                       class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all"
                                       id="prix_vente_ttc"
                                       name="prix_vente_ttc"
                                       value="{{ old('prix_vente_ttc') }}"
                                       required
                                       min="0"
                                       step="0.01">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Prix de vente pour une unité (taxes comprises)</p>
                            @error('prix_vente_ttc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Champs cachés pour la compatibilité -->
                        <input type="hidden" id="marge" name="marge" value="{{ old('marge', 20) }}">
                        <input type="hidden" id="tva" name="tva" value="{{ old('tva', 18) }}">
                        <input type="hidden" id="prix_vente_ht" name="prix_vente_ht" value="{{ old('prix_vente_ht') }}">
                        <input type="hidden" id="valeur_marge" name="valeur_marge" value="{{ old('valeur_marge') }}">
                        <input type="hidden" id="valeur_tva" name="valeur_tva" value="{{ old('valeur_tva') }}">
                    </div>
                    
                    <!-- Note explicative -->
                    <div class="mb-6 p-4 bg-blue-50/70 rounded-lg border border-blue-100">
                        <p class="text-blue-800 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Important :</strong> Le prix d'achat est ce que vous payez au fournisseur. Le prix de vente unitaire est ce que le client paie pour une unité. Les conditionnements ci-dessous permettent de définir des prix pour différentes quantités (ex: prix spécial pour une douzaine).
                        </p>
                    </div>
                    
                    <!-- Conditionnements prédéfinis -->
                    <div class="mb-6 p-4 bg-indigo-50/70 rounded-lg border border-indigo-100">
                        <h4 class="text-md font-medium text-indigo-800 mb-3">Conditionnements de vente</h4>
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="unité" data-quantite="1">
                                <span class="text-indigo-700 font-medium">Unité</span>
                                <span class="text-gray-500 text-sm">1 unité</span>
                            </button>
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="2 unités" data-quantite="2">
                                <span class="text-indigo-700 font-medium">2 Unités</span>
                                <span class="text-gray-500 text-sm">2 unités</span>
                            </button>
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="3 unités" data-quantite="3">
                                <span class="text-indigo-700 font-medium">3 Unités</span>
                                <span class="text-gray-500 text-sm">3 unités</span>
                            </button>
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="quart" data-quantite="4">
                                <span class="text-indigo-700 font-medium">Quart</span>
                                <span class="text-gray-500 text-sm">4 unités</span>
                            </button>
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="demi" data-quantite="6">
                                <span class="text-indigo-700 font-medium">Demi</span>
                                <span class="text-gray-500 text-sm">6 unités</span>
                            </button>
                            <button type="button" class="add-preset-cond px-3 py-2 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors flex flex-col items-center shadow-sm" 
                                    data-type="douzaine" data-quantite="12">
                                <span class="text-indigo-700 font-medium">Douzaine</span>
                                <span class="text-gray-500 text-sm">12 unités</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-4 gap-4 font-medium text-indigo-700">
                            <div>Type</div>
                            <div>Quantité</div>
                            <div>Prix de vente (FCFA)</div>
                            <div></div>
                        </div>
                        
                        <div id="conditionnements-list" class="space-y-3">
                            @foreach(old('conditionnements', []) as $i => $cond)
                                <div class="grid grid-cols-4 gap-4 items-center">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-box text-indigo-400"></i>
                                        </div>
                                        <input type="text" name="conditionnements[{{ $i }}][type]" value="{{ $cond['type'] }}" 
                                            class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                            required placeholder="Ex: unité, quart...">
                                    </div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-hashtag text-indigo-400"></i>
                                        </div>
                                        <input type="number" name="conditionnements[{{ $i }}][quantite]" value="{{ $cond['quantite'] }}" 
                                            class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                            min="1" required>
                                    </div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-money-bill text-indigo-400"></i>
                                        </div>
                                        <input type="number" name="conditionnements[{{ $i }}][prix]" value="{{ $cond['prix'] }}" 
                                            class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                                            min="0" step="0.01" required>
                                    </div>
                                    <button type="button" class="remove-cond inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors duration-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="button" id="add-cond" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-tr from-indigo-600 to-purple-500 text-white rounded-lg shadow-md hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-plus"></i>
                            Ajouter un conditionnement personnalisé
                        </button>
                    </div>
                </div>

                <!-- Section 4: Stock Initial & Emplacement -->
                <div class="bg-white/70 backdrop-blur-md border border-indigo-100 rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-indigo-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-warehouse text-indigo-500"></i> Stock Initial & Date de péremption
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="stock_initial" class="block text-sm font-medium text-indigo-700 mb-1">Stock initial <span class="text-red-500">*</span></label>
                            <input type="number" id="stock_initial" name="stock_initial" value="{{ old('stock_initial', 0) }}" required class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <span class="ml-2 relative group align-middle">
    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
        Quantité en stock lors de la création du produit.
        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
    </span>
</span>
                            @error('stock_initial') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="seuil_alerte" class="block text-sm font-medium text-indigo-700 mb-1">Seuil d'alerte <span class="text-red-500">*</span></label>
                            <input type="number" id="seuil_alerte" name="seuil_alerte" value="{{ old('seuil_alerte', 10) }}" required class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <span class="ml-2 relative group align-middle">
    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
        Niveau de stock minimum avant déclenchement d'une alerte.
        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
    </span>
</span>
                            @error('seuil_alerte') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="date_peremption" class="block text-sm font-medium text-indigo-700 mb-1">Date de péremption <span class="text-red-500">*</span></label>
                            <input type="date" id="date_peremption" name="date_peremption" value="{{ old('date_peremption') }}" required class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <div class="flex items-center mt-2">
                                <input type="checkbox" id="no_peremption" name="no_peremption" class="mr-2">
                                <label for="no_peremption" class="text-sm text-gray-600">Pas de péremption</label>
                            </div>
                            <span class="ml-2 relative group align-middle">
    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
        Obligatoire : date d'expiration du produit.
        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
    </span>
</span>
                            @error('date_peremption') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            
                            <div class="mt-4">
                                <label for="delai_alerte_peremption" class="block text-sm font-medium text-indigo-700 mb-1">Délai d'alerte avant péremption (jours)</label>
                                <div class="flex items-center">
                                    <input type="number" id="delai_alerte_peremption" name="delai_alerte_peremption" value="{{ old('delai_alerte_peremption') }}" min="1" max="365" placeholder="30" class="block w-1/2 rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                                    <span class="ml-2 text-sm text-gray-500">Vide = 30 jours par défaut</span>
                                    <span class="ml-2 relative group align-middle">
    <i class="fas fa-info-circle text-indigo-400 cursor-pointer"></i>
    <span class="absolute left-1/2 z-10 -translate-x-1/2 mt-2 hidden group-hover:block group-focus:block bg-white border border-indigo-200 text-indigo-900 text-xs rounded-lg shadow-lg px-4 py-2 whitespace-nowrap transition-all duration-200">
        Nombre de jours avant la péremption pour déclencher l'alerte.
        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-l border-t border-indigo-200 rotate-45"></span>
    </span>
</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Spécifiez combien de jours avant la péremption l'alerte doit être déclenchée</p>
                                @error('delai_alerte_peremption') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <!-- Champs cachés pour les emplacements -->
                        <input type="hidden" id="emplacement_rayon" name="emplacement_rayon" value="{{ old('emplacement_rayon', '') }}">
                        <input type="hidden" id="emplacement_etagere" name="emplacement_etagere" value="{{ old('emplacement_etagere', '') }}">
                    </div>
                </div>

                <!-- Bouton Soumettre -->
                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-tr from-green-500 to-emerald-600 text-white font-bold text-lg shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-save"></i>
                        Enregistrer le Produit
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
    // Gestion de l'affichage du champ "valeur_mesure"
    const uniteMesureEl = document.getElementById('unite_mesure');
    const valeurMesureContainerEl = document.getElementById('valeur_mesure_container');
    if (uniteMesureEl && valeurMesureContainerEl) {
        uniteMesureEl.addEventListener('change', function() {
            if (this.value === 'Autre') {
                valeurMesureContainerEl.classList.remove('hidden');
            } else {
                valeurMesureContainerEl.classList.add('hidden');
            }
        });
        // Trigger change on load if 'Autre' is pre-selected
        if (uniteMesureEl.value === 'Autre') {
             valeurMesureContainerEl.classList.remove('hidden');
        }
    }

    // Image Preview Script
    const imageInput = document.getElementById('image');
    const imagePreviewEl = document.getElementById('imagePreview');
    const imagePreviewPlaceholderEl = document.getElementById('imagePreviewPlaceholder');
    const imagePreviewContainerEl = document.getElementById('imagePreviewContainer');

    window.previewImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if(imagePreviewEl && imagePreviewPlaceholderEl) {
                    imagePreviewEl.src = e.target.result;
                    imagePreviewEl.classList.remove('hidden');
                    imagePreviewPlaceholderEl.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        } else {
             if(imagePreviewEl && imagePreviewPlaceholderEl) {
                imagePreviewEl.src = "{{ asset('assets/img/placeholder-image.png') }}"; // Default or placeholder
                imagePreviewEl.classList.add('hidden');
                imagePreviewPlaceholderEl.classList.remove('hidden');
            }
        }
    }
    
    // Drag and drop for image
    if (imagePreviewContainerEl && imageInput) {
        imagePreviewContainerEl.addEventListener('click', () => imageInput.click());

        imagePreviewContainerEl.addEventListener('dragover', (event) => {
            event.preventDefault();
            imagePreviewContainerEl.classList.add('border-blue-500', 'bg-blue-50');
        });

        imagePreviewContainerEl.addEventListener('dragleave', () => {
            imagePreviewContainerEl.classList.remove('border-blue-500', 'bg-blue-50');
        });

        imagePreviewContainerEl.addEventListener('drop', (event) => {
            event.preventDefault();
            imagePreviewContainerEl.classList.remove('border-blue-500', 'bg-blue-50');
            if (event.dataTransfer.files.length > 0) {
                imageInput.files = event.dataTransfer.files;
                previewImage(imageInput); // Trigger preview
            }
        });
    }
     // If there's an old image value (e.g., validation error), trigger preview
    @if(old('image_preview_src'))
        if(imagePreviewEl && imagePreviewPlaceholderEl) {
            imagePreviewEl.src = "{{ old('image_preview_src') }}";
            imagePreviewEl.classList.remove('hidden');
            imagePreviewPlaceholderEl.classList.add('hidden');
        }
    @elseif(isset($produit) && $produit->image_url)
        if(imagePreviewEl && imagePreviewPlaceholderEl) {
            imagePreviewEl.src = "{{ $produit->image_url }}";
            imagePreviewEl.classList.remove('hidden');
            imagePreviewPlaceholderEl.classList.add('hidden');
        }
    @endif

    let condIndex = {{ count(old('conditionnements', [])) }};
    
    // Fonction pour ajouter un conditionnement
    function addConditionnement(type, quantite, prix = '') {
        const list = document.getElementById('conditionnements-list');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-4 gap-4 items-center';
        row.innerHTML = `
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-box text-indigo-400"></i>
                </div>
                <input type="text" name="conditionnements[${condIndex}][type]" value="${type}" 
                    class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                    required placeholder="Ex: unité, quart...">
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-hashtag text-indigo-400"></i>
                </div>
                <input type="number" name="conditionnements[${condIndex}][quantite]" value="${quantite}" 
                    class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                    min="1" required>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-money-bill text-indigo-400"></i>
                </div>
                <input type="number" name="conditionnements[${condIndex}][prix]" value="${prix}" 
                    class="pl-10 block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                    min="0" step="0.01" required>
            </div>
            <button type="button" class="remove-cond inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        `;
        list.appendChild(row);
        
        // Focus sur le champ prix si c'est un conditionnement prédéfini
        if (prix === '') {
            const priceInput = row.querySelector('input[name$="[prix]"]');
            if (priceInput) {
                setTimeout(() => priceInput.focus(), 100);
            }
        }
        
        condIndex++;
    }
    
    // Gestion du bouton d'ajout de conditionnement personnalisé
    document.getElementById('add-cond').addEventListener('click', function() {
        addConditionnement('', '');
    });
    
    // Gestion des boutons de conditionnements prédéfinis
    document.querySelectorAll('.add-preset-cond').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const quantite = this.dataset.quantite;
            
            // Calculer un prix suggéré basé sur le prix unitaire et la quantité
            let prixSuggere = '';
            const prixUnitaire = parseFloat(document.getElementById('prix_vente_ttc')?.value || 0);
            if (prixUnitaire > 0) {
                prixSuggere = Math.round(prixUnitaire * parseFloat(quantite));
            }
            
            addConditionnement(type, quantite, prixSuggere);
        });
    });
    
    // Gestion de la suppression de conditionnement
    document.getElementById('conditionnements-list').addEventListener('click', function(e) {
        if (e.target.closest('.remove-cond')) {
            e.target.closest('.grid').remove();
        }
    });
    
    // Mise à jour automatique des prix de conditionnement quand le prix de vente TTC change
    document.getElementById('prix_vente_ttc')?.addEventListener('change', function() {
        const prixUnitaire = parseFloat(this.value || 0);
        if (prixUnitaire > 0) {
            document.querySelectorAll('#conditionnements-list .grid').forEach(row => {
                const quantiteInput = row.querySelector('input[name$="[quantite]"]');
                const prixInput = row.querySelector('input[name$="[prix]"]');
                
                if (quantiteInput && prixInput && prixInput.value === '') {
                    const quantite = parseFloat(quantiteInput.value || 0);
                    if (quantite > 0) {
                        prixInput.value = Math.round(prixUnitaire * quantite);
                    }
                }
            });
        }
    });

    const dateInput = document.getElementById('date_peremption');
    const noPeremption = document.getElementById('no_peremption');
    if (dateInput && noPeremption) {
        noPeremption.addEventListener('change', function() {
            if (this.checked) {
                dateInput.value = '';
                dateInput.setAttribute('disabled', 'disabled');
                dateInput.removeAttribute('required');
            } else {
                dateInput.removeAttribute('disabled');
                dateInput.setAttribute('required', 'required');
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Style pour s'assurer que l'image d'aperçu ne dépasse pas son conteneur */
    #imagePreview {
        object-fit: contain; /* ou 'cover' selon le rendu souhaité */
    }
    /* Amélioration visuelle pour le drag & drop */
    #imagePreviewContainer.border-blue-500 {
        border-color: #3b82f6; /* blue-500 */
    }
    #imagePreviewContainer.bg-blue-50 {
        background-color: #eff6ff; /* blue-50 */
    }
</style>
@endpush