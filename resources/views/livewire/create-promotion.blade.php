<div>
    @if ($currentStep == 1)
        <!-- Step 1: Informations générales -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                Informations générales de la promotion
            </h3>
            {{-- Champs pour le type, la valeur, les dates, la description --}}
            {{-- ... existing code ... --}}
        </div>
    @elseif ($currentStep == 2)
        <!-- Step 2: Sélection des produits -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                Sélection des produits concernés
            </h3>
            {{-- Champ pour sélectionner les produits (sera amélioré plus tard) --}}
            {{-- ... existing code ... --}}
        </div>
    @elseif ($currentStep == 3)
        <!-- Step 3: Révision et confirmation -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                Révision et Création
            </h3>
            {{-- Résumé de la promotion et bouton de soumission finale --}}
            {{-- ... existing code ... --}}
        </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex justify-between mt-8">
        @if ($currentStep > 1)
            <button wire:click="previousStep" type="button" class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition">
                Précédent
            </button>
        @endif

        @if ($currentStep < 3)
            <button wire:click="nextStep" type="button" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-xl hover:bg-indigo-700 transition">
                Suivant
            </button>
        @else
            <button wire:click="submitForm" type="submit" class="px-6 py-3 rounded-xl bg-green-600 text-white font-bold shadow-xl hover:bg-green-700 transition">
                Créer les promotions
            </button>
        @endif
    </div>
</div>
