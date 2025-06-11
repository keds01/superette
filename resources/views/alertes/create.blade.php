@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de page -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 p-3 rounded-2xl shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Nouvelle Alerte</h2>
                    <p class="mt-1 text-gray-600">Configurez une alerte pour surveiller le stock ou la péremption d'un produit</p>
                </div>
            </div>
            <a href="{{ route('alertes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg border border-purple-200 bg-white text-purple-700 font-medium shadow-sm hover:bg-purple-50 hover:text-purple-900 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <!-- En-tête du formulaire -->
            <div class="border-b border-gray-200">
                <div class="px-8 py-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-purple-100 rounded-lg">
                            <i class="fas fa-bell text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Détails de l'alerte</h3>
                            <p class="text-gray-500 text-sm">Tous les champs marqués * sont obligatoires</p>
                        </div>
                    </div>
                    <div class="bg-indigo-50 px-3 py-1.5 rounded-full text-xs font-medium text-indigo-700">
                        <span>Formulaire de création</span>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('alertes.store') }}" method="POST" class="p-8 space-y-8">
                @csrf
                
                <!-- Bloc résumé produit (initialement caché) -->
                <div id="resume-produit" class="mb-8 hidden">
                    <div class="relative overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm">
                        <!-- Fond décoratif avec dégradé -->
                        <div class="absolute left-0 top-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                        
                        <!-- En-tête du bloc -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded-lg bg-indigo-100">
                                    <i class="fas fa-info-circle text-indigo-600"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-700">Informations du produit sélectionné</h4>
                            </div>
                            <span id="produit-statut" class="text-xs bg-green-100 text-green-800 px-2.5 py-1 rounded-full font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                Produit actif
                            </span>
                        </div>
                        
                        <!-- Contenu -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Catégorie -->
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 p-2 bg-indigo-100 rounded-lg">
                                    <i class="fas fa-tag text-indigo-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">CATÉGORIE</p>
                                    <p class="text-base font-medium text-gray-900 mt-1" id="categorie-produit">--</p>
                                </div>
                            </div>
                            
                            <!-- Stock actuel -->
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-cubes text-green-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">STOCK ACTUEL</p>
                                    <p class="text-base font-medium text-gray-900 mt-1" id="stock-produit">--</p>
                                </div>
                            </div>
                            
                            <!-- Date de péremption -->
                            <div class="flex items-center">
                                <div class="p-3 bg-red-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-times text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">DATE DE PEREMPTION</p>
                                    <p id="date-peremption-produit" class="text-lg font-semibold text-gray-700">--</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                        <p class="font-bold">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Bloc configuration de l'alerte -->
                <div class="mb-8">
                    <div class="relative overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm">
                        <!-- Fond décoratif -->
                        <div class="absolute left-0 top-0 w-full h-1 bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-400"></div>
                        
                        <!-- En-tête du bloc -->
                        <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded-lg bg-orange-100">
                                    <i class="fas fa-bell text-orange-600"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-700">Paramètres de l'alerte</h4>
                            </div>
                        </div>
                        
                        <!-- Sélection du produit -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="mb-5">
                                <label for="produit_id" class="block text-sm font-medium text-gray-700 mb-1">Produit *</label>
                                <select id="produit_id" name="produit_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                                            {{ $produit->nom }} ({{ $produit->categorie->nom }} - Stock: {{ $produit->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Type d'alerte avec affichage visuel -->
                        <div class="p-6 border-b border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Type d'alerte *</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Option 1: Seuil minimum -->
                                <div class="relative">
                                    <input type="radio" id="type_seuil_minimum" name="type" value="seuil_minimum" 
                                        class="peer absolute h-0 w-0 opacity-0" {{ old('type', 'seuil_minimum') == 'seuil_minimum' ? 'checked' : '' }} required>
                                    <label for="type_seuil_minimum" class="flex flex-col gap-2 p-4 border-2 rounded-xl cursor-pointer 
                                        peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <div class="p-2 bg-red-100 rounded-lg">
                                                <i class="fas fa-arrow-down text-red-600"></i>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 peer-checked:border-red-500 peer-checked:bg-red-500 transition-colors"></div>
                                        </div>
                                        <h4 class="text-base font-medium">Seuil minimum</h4>
                                        <p class="text-sm text-gray-600">
                                            Alerte quand le stock descend sous un seuil critique
                                        </p>
                                    </label>
                                </div>
                                
                                <!-- Option 2: Seuil maximum -->
                                <div class="relative">
                                    <input type="radio" id="type_seuil_maximum" name="type" value="seuil_maximum" 
                                        class="peer absolute h-0 w-0 opacity-0" {{ old('type') == 'seuil_maximum' ? 'checked' : '' }} required>
                                    <label for="type_seuil_maximum" class="flex flex-col gap-2 p-4 border-2 rounded-xl cursor-pointer 
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <div class="p-2 bg-blue-100 rounded-lg">
                                                <i class="fas fa-arrow-up text-blue-600"></i>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-colors"></div>
                                        </div>
                                        <h4 class="text-base font-medium">Seuil maximum</h4>
                                        <p class="text-sm text-gray-600">
                                            Alerte quand le stock dépasse une limite maximale
                                        </p>
                                    </label>
                                </div>
                                
                                <!-- Option 3: Péremption -->
                                <div class="relative">
                                    <input type="radio" id="type_peremption" name="type" value="peremption" 
                                        class="peer absolute h-0 w-0 opacity-0" {{ old('type') == 'peremption' ? 'checked' : '' }} required>
                                    <label for="type_peremption" class="flex flex-col gap-2 p-4 border-2 rounded-xl cursor-pointer 
                                        peer-checked:border-amber-500 peer-checked:bg-amber-50 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <div class="p-2 bg-amber-100 rounded-lg">
                                                <i class="fas fa-calendar-times text-amber-600"></i>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 peer-checked:border-amber-500 peer-checked:bg-amber-500 transition-colors"></div>
                                        </div>
                                        <h4 class="text-base font-medium">Péremption</h4>
                                        <p class="text-sm text-gray-600">
                                            Alerte quand un produit approche de sa date de péremption
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seuil (visible si alerte de type minimum ou maximum) -->
                        <div id="seuil-field" class="p-6 border-b border-gray-200">
                            <div class="mb-4">
                                <label for="seuil" class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte *</label>
                                <div class="flex">
                                    <input type="number" id="seuil" name="seuil" min="0" step="1" 
                                        value="{{ old('seuil') }}" 
                                        class="flex-grow px-4 py-2.5 rounded-l-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="inline-flex items-center px-4 py-2.5 bg-gray-50 text-gray-600 border border-l-0 border-gray-300 rounded-r-lg">
                                        unités
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Nombre d'unités qui déclenchera l'alerte</p>
                            </div>
                        </div>
                        
                        <!-- Période (visible si alerte de type péremption) -->
                        <div id="periode-field" class="p-6 border-b border-gray-200 hidden">
                            <div class="mb-4">
                                <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">Jours avant péremption *</label>
                                <div class="flex">
                                    <input type="number" id="periode" name="periode" min="1" step="1" 
                                        value="{{ old('periode') }}" 
                                        class="flex-grow px-4 py-2.5 rounded-l-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="inline-flex items-center px-4 py-2.5 bg-gray-50 text-gray-600 border border-l-0 border-gray-300 rounded-r-lg">
                                        jours
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Nombre de jours avant la péremption qui déclenchera l'alerte</p>
                            </div>
                        </div>
                        
                        <!-- Message personnalisé -->
                        <div class="p-6">
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message personnalisé</label>
                                <textarea id="message" name="message" rows="2" 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Message optionnel qui s'affichera avec cette alerte">{{ old('message') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('alertes.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-medium rounded-lg shadow hover:shadow-lg hover:-translate-y-0.5 transform transition-all duration-200">
                        <i class="fas fa-save mr-2"></i> Enregistrer l'alerte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resumeProduit = document.getElementById('resume-produit');
        const produitSelect = document.getElementById('produit_id');
        
        // Animation pour afficher/masquer des éléments
        function showWithFadeIn(element) {
            element.style.opacity = 0;
            element.classList.remove('hidden');
            setTimeout(() => { element.style.opacity = 1; }, 10);
        }
        
        function hideWithFadeOut(element) {
            element.style.opacity = 0;
            setTimeout(() => { element.classList.add('hidden'); }, 300);
        }
        
        // Gestion du changement de type d'alerte
        function toggleAlerteFields() {
            const typeValue = document.querySelector('input[name="type"]:checked').value;
            const seuilField = document.getElementById('seuil-field');
            const periodeField = document.getElementById('periode-field');
            
            if (typeValue === 'peremption') {
                hideWithFadeOut(seuilField);
                setTimeout(() => { showWithFadeIn(periodeField); }, 300);
                document.getElementById('periode').required = true;
                document.getElementById('seuil').required = false;
            } else {
                hideWithFadeOut(periodeField);
                setTimeout(() => { showWithFadeIn(seuilField); }, 300);
                document.getElementById('periode').required = false;
                document.getElementById('seuil').required = true;
            }
        }
        
        // Écouter les changements de type d'alerte
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', toggleAlerteFields);
        });
        
        // Initialiser l'affichage des champs selon le type sélectionné
        toggleAlerteFields();
        
        // Gérer les changements de produit sélectionné
        produitSelect.addEventListener('change', function() {
            if (!this.value) {
                hideWithFadeOut(resumeProduit);
                return;
            }
            
            // Montrer l'animation de chargement
            resumeProduit.classList.add('animate-pulse');
            showWithFadeIn(resumeProduit);
            
            // Récupérer les informations du produit via AJAX
            fetch(`/api/produits/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const produit = data.produit;
                        
                        // Mettre à jour les informations du produit
                        document.getElementById('categorie-produit').textContent = produit.categorie ? produit.categorie.nom : 'Non catégorisé';
                        document.getElementById('stock-produit').textContent = `${produit.stock} unités`;
                        
                        if (produit.date_peremption) {
                            const datePeremption = new Date(produit.date_peremption);
                            document.getElementById('date-peremption-produit').textContent = datePeremption.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
                        } else {
                            document.getElementById('date-peremption-produit').textContent = 'Non spécifiée';
                        }
                        
                        // Gérer l'affichage du statut du produit
                        const statutElement = document.getElementById('produit-statut');
                        if (produit.is_active) {
                            statutElement.className = 'text-xs bg-green-100 text-green-800 px-2.5 py-1 rounded-full font-medium flex items-center gap-1';
                            statutElement.innerHTML = '<i class="fas fa-check-circle"></i> Produit actif';
                        } else {
                            statutElement.className = 'text-xs bg-red-100 text-red-800 px-2.5 py-1 rounded-full font-medium flex items-center gap-1';
                            statutElement.innerHTML = '<i class="fas fa-times-circle"></i> Produit inactif';
                        }
                        
                        // Retirer l'animation de chargement
                        resumeProduit.classList.remove('animate-pulse');
                    } else {
                        console.error('Erreur:', data.message);
                        hideWithFadeOut(resumeProduit);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    hideWithFadeOut(resumeProduit);
                    
                    // Afficher une notification d'erreur
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-md z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-red-500">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">Une erreur est survenue lors du chargement des détails du produit.</p>
                            </div>
                            <button type="button" class="ml-auto text-red-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                    notification.querySelector('button').addEventListener('click', () => notification.remove());
                });
        });
        
        // Déclencher le chargement si un produit est pré-sélectionné
        if (produitSelect.value) {
            produitSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection
