@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3 animate-fade-in">
                        <svg class="w-8 h-8 text-indigo-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Nouvelle Réception
                    </h2>
                    <p class="mt-2 text-lg text-gray-500">Enregistrez une nouvelle réception de marchandises</p>
                </div>
                <a href="{{ route('receptions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            @if(session('error'))
                <div class="mb-6">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-pulse" role="alert">
                        <strong class="font-bold">Erreur : </strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            <form action="{{ route('receptions.store') }}" method="POST" class="space-y-8">
                <!-- Mode d'enregistrement direct sans validation complexe -->
                <input type="hidden" name="direct_save" value="1">
                @csrf

                @if(isset($commande) && $commande && isset($commande->details) && count($commande->details) > 0) 
                    {{-- MODE COMMANDE LIÉE : affichage strict --}}
                    <div class="bg-white rounded-xl border border-indigo-200 p-6 shadow-sm mb-6">
                        <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10-5h2a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h2" />
                            </svg>
                            Commande liée
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div><span class="text-gray-500">N° Commande :</span> <span class="font-bold text-gray-900">{{ $commande->id }}</span></div>
                            <div><span class="text-gray-500">Fournisseur :</span> <span class="font-bold text-gray-900">{{ $commande->fournisseur->nom }}</span></div>
                            <div><span class="text-gray-500">Date :</span> <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div><span class="text-gray-500">Statut :</span> <span class="font-bold text-indigo-700">{{ ucfirst($commande->statut) }}</span></div>
    <div><span class="text-gray-500">Montant total :</span> <span class="font-bold text-green-700">{{ number_format($commande->montant_total, 2, ',', ' ') }} {{ $commande->devise ?? 'FCFA' }}</span></div>
</div>
                    </div>

                    <input type="hidden" name="fournisseur_id" value="{{ $commande->fournisseur_id }}" />
                    <input type="hidden" name="commande_id" value="{{ $commande->id }}" />
                    
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de réception</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date_reception" class="block text-sm font-medium text-gray-700 mb-1">Date de réception <span class="text-red-500">*</span></label>
                                <input type="date" id="date_reception" name="date_reception" value="{{ old('date_reception', date('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('date_reception')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="mode_paiement" class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement <span class="text-red-500">*</span></label>
                                <select id="mode_paiement" name="mode_paiement" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                    <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="autre" {{ old('mode_paiement') == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('mode_paiement')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="col-span-2">
                                <label for="numero_facture" class="block text-sm font-medium text-gray-700 mb-1">Numéro de facture</label>
                                <input type="text" id="numero_facture" name="numero_facture" value="{{ old('numero_facture', isset($commande) && $commande && $commande->numero_commande ? $commande->numero_commande : '') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('numero_facture')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Produits à réceptionner
                        </h3>
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-indigo-50">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Produit</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Qté commandée</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Qté reçue</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Prix unitaire ({{ $commande->devise ?? 'FCFA' }})</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Date péremption</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Total ligne ({{ $commande->devise ?? 'FCFA' }})</th>
                                </tr>
                            </thead>
                            <tbody id="reception-produits-body">
                                @foreach($commande->details as $i => $detail)
                                    <tr class="bg-white border-b">
                                        <td class="px-4 py-2">{{ $detail->produit->nom }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" class="w-20 text-center bg-gray-100 rounded" value="{{ $detail->quantite }}" readonly />
                                            <input type="hidden" name="produits[{{ $i }}][produit_id]" value="{{ $detail->produit_id }}" />
                                            <!-- Champ prix_unitaire déplacé plus bas -->
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" name="produits[{{ $i }}][quantite]" required min="0" max="{{ $detail->quantite }}" step="0.01" value="{{ old('produits.'.$i.'.quantite', $detail->quantite) }}" class="w-20 text-center rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 reception-qte" />
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-end gap-1">
                                                <input type="number" name="produits[{{ $i }}][prix_unitaire]" value="{{ old('produits.'.$i.'.prix_unitaire', $detail->prix_unitaire) }}" step="0.01" min="0" class="w-28 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-right" required />
                                                <span class="text-xs text-gray-500">{{ $commande->devise ?? 'FCFA' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="date" name="produits[{{ $i }}][date_peremption]" value="{{ old('produits.'.$i.'.date_peremption') }}" class="w-36 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <span class="ligne-total font-semibold text-gray-800">{{ number_format($detail->quantite * $detail->prix_unitaire, 2, ',', ' ') }}</span> {{ $commande->devise ?? 'FCFA' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="flex justify-end mt-4">
                            <span class="text-lg font-bold text-indigo-700">Total réceptionné : <span id="total-reception">0.00</span> {{ $commande->devise ?? 'FCFA' }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Notes / Description (facultatif)</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Remarques sur la réception...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- MODE FORMULAIRE LIBRE --}}
                    @include('receptions._form_libre')
                @endif

                <div class="flex justify-end gap-4 mt-8">
                    <a href="{{ route('receptions.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        Enregistrer la réception
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($commande) && $commande && isset($commande->details) && count($commande->details) > 0)
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qteInputs = document.querySelectorAll('.reception-qte');
            const totalReception = document.getElementById('total-reception');
            const prixInputs = document.querySelectorAll('input[name^="produits"][name$="[prix_unitaire]"]');
            const devise = "{{ $commande->devise ?? 'FCFA' }}";

            function updateTotal() {
                let total = 0;
                qteInputs.forEach(function (qteInput, idx) {
                    const prix = parseFloat(prixInputs[idx].value) || 0;
                    const qte = parseFloat(qteInput.value) || 0;
                    total += prix * qte;
                });
                totalReception.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + devise;
            }
            qteInputs.forEach(function (input) {
                input.addEventListener('input', updateTotal);
            });
            prixInputs.forEach(function (input) {
                input.addEventListener('input', updateTotal);
            });
            updateTotal();
        });
    </script>
    @endpush
    @endif
@endsection