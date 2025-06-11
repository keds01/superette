@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-indigo-900">Nouvelle Remise</h2>
                    <p class="mt-2 text-gray-600">Créez une remise sur une vente existante. Les remises permettent d'appliquer une réduction à une vente sélectionnée.</p>
                </div>
                <div>
                    <a href="{{ route('remises.index') }}" class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour du label de la valeur selon le type
        document.addEventListener('DOMContentLoaded', function() {
            const typeRemise = document.getElementById('type_remise');
            if (typeRemise) {
                typeRemise.addEventListener('change', function() {
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
                typeRemise.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection 