@extends('layouts.app')

@section('title', 'Modifier la Vente #' . $vente->numero)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la Vente #{{ $vente->numero }}</h5>
                    <div>
                        <a href="{{ route('ventes.show', $vente) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        @if($vente->statut === 'en_cours' && auth()->user()->can('ventes.edit'))
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAnnuler">
                                <i class="fas fa-times"></i> Annuler la vente
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($vente->statut !== 'en_cours')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Cette vente ne peut plus être modifiée car son statut est "{{ $vente->statut }}".
                        </div>
                    @else
                        <form action="{{ route('ventes.update', $vente) }}" method="POST" id="formVente">
                            @csrf
                            @method('PUT')
                            
                            <!-- Informations générales -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Informations Générales</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="client_id" class="form-label">Client</label>
                                                <select name="client_id" id="client_id" class="form-select" required {{ $vente->statut !== 'en_cours' ? 'disabled' : '' }}>
                                                    <option value="">Sélectionner un client...</option>
                                                    @foreach($clients as $client)
                                                        <option value="{{ $client->id }}" {{ old('client_id', $vente->client_id) == $client->id ? 'selected' : '' }}>
                                                            {{ $client->nom }} {{ $client->prenom }} - {{ $client->telephone }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('client_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="type_vente" class="form-label">Type de vente</label>
                                                <select name="type_vente" id="type_vente" class="form-select" required {{ $vente->statut !== 'en_cours' ? 'disabled' : '' }}>
                                                    <option value="">Sélectionner...</option>
                                                    <option value="sur_place" {{ old('type_vente', $vente->type_vente) == 'sur_place' ? 'selected' : '' }}>Sur place</option>
                                                    <option value="a_emporter" {{ old('type_vente', $vente->type_vente) == 'a_emporter' ? 'selected' : '' }}>À emporter</option>
                                                    <option value="livraison" {{ old('type_vente', $vente->type_vente) == 'livraison' ? 'selected' : '' }}>Livraison</option>
                                                </select>
                                                @error('type_vente')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="notes" class="form-label">Notes</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="2" {{ $vente->statut !== 'en_cours' ? 'disabled' : '' }}>{{ old('notes', $vente->notes) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Récapitulatif</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th style="width: 200px">Sous-total HT</th>
                                                        <td class="text-end" id="sousTotal">{{ number_format($vente->montant_ht, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                    <tr>
                                                        <th>TVA (20%)</th>
                                                        <td class="text-end" id="montantTva">{{ number_format($vente->montant_tva, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Remises</th>
                                                        <td class="text-end text-danger" id="montantRemise">{{ number_format($vente->montant_remise, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <th>Total TTC</th>
                                                        <td class="text-end fw-bold" id="montantTotal">{{ number_format($vente->montant_total, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Montant payé</th>
                                                        <td class="text-end" id="montantPaye">{{ number_format($vente->montant_paye, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Reste à payer</th>
                                                        <td class="text-end" id="resteAPayer">{{ number_format($vente->montant_total - $vente->montant_paye, 2, ',', ' ') }} €</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produits -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Produits</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="tableProduits">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%">Produit</th>
                                                    <th style="width: 15%">Prix unitaire</th>
                                                    <th style="width: 15%">Quantité</th>
                                                    <th style="width: 15%">Remise</th>
                                                    <th style="width: 15%">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="ligneTemplate" style="display: none;">
                                                    <td>
                                                        <select name="produits[INDEX][produit_id]" class="form-select produit-select" required>
                                                            <option value="">Sélectionner...</option>
                                                            @foreach($produits as $produit)
                                                                <option value="{{ $produit->id }}" 
                                                                    data-prix="{{ $produit->prix_vente }}"
                                                                    data-stock="{{ $produit->quantite }}">
                                                                    {{ $produit->nom }} (Stock: {{ $produit->quantite }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" name="produits[INDEX][prix_unitaire]" class="form-control prix-unitaire" step="0.01" readonly>
                                                            <span class="input-group-text">€</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" name="produits[INDEX][quantite]" class="form-control quantite" min="1" value="1" required>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm incrementer">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm decrementer">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" name="produits[INDEX][remise]" class="form-control remise" step="0.01" min="0" value="0">
                                                            <span class="input-group-text">€</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control total-ligne" readonly>
                                                            <span class="input-group-text">€</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm supprimer-ligne">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @foreach($vente->details as $index => $detail)
                                                    <tr id="ligne_{{ $index }}">
                                                        <td>
                                                            <select name="produits[{{ $index }}][produit_id]" class="form-select produit-select" required>
                                                                <option value="">Sélectionner...</option>
                                                                @foreach($produits as $produit)
                                                                    <option value="{{ $produit->id }}" 
                                                                        data-prix="{{ $produit->prix_vente }}"
                                                                        data-stock="{{ $produit->quantite }}"
                                                                        {{ $detail->produit_id == $produit->id ? 'selected' : '' }}>
                                                                        {{ $produit->nom }} (Stock: {{ $produit->quantite }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" name="produits[{{ $index }}][prix_unitaire]" class="form-control prix-unitaire" step="0.01" value="{{ $detail->prix_unitaire }}" readonly>
                                                                <span class="input-group-text">€</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" name="produits[{{ $index }}][quantite]" class="form-control quantite" min="1" value="{{ $detail->quantite }}" required>
                                                                <button type="button" class="btn btn-outline-secondary btn-sm incrementer">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary btn-sm decrementer">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" name="produits[{{ $index }}][remise]" class="form-control remise" step="0.01" min="0" value="{{ $detail->remise }}">
                                                                <span class="input-group-text">€</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" class="form-control total-ligne" value="{{ $detail->montant_total }}" readonly>
                                                                <span class="input-group-text">€</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm supprimer-ligne">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" class="btn btn-success" id="ajouterLigne">
                                        <i class="fas fa-plus"></i> Ajouter un produit
                                    </button>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="submitVente" {{ $vente->statut !== 'en_cours' ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div class="modal fade" id="modalAnnuler" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer l'annulation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir annuler cette vente ? Cette action est irréversible.</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Attention : Si des paiements ont déjà été effectués, ils devront être remboursés.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmer l'annulation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation des select2
    $('#client_id, #type_vente').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    let indexLigne = {{ count($vente->details) }};

    // Fonction pour ajouter une nouvelle ligne
    function ajouterLigne() {
        const template = $('#ligneTemplate').clone();
        template.attr('id', 'ligne_' + indexLigne);
        template.find('[name*="INDEX"]').each(function() {
            $(this).attr('name', $(this).attr('name').replace('INDEX', indexLigne));
        });
        template.show();
        $('#tableProduits tbody').append(template);
        
        // Initialiser select2 pour la nouvelle ligne
        template.find('.produit-select').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        indexLigne++;
        calculerTotaux();
    }

    // Gestionnaire pour le bouton d'ajout de ligne
    $('#ajouterLigne').click(ajouterLigne);

    // Gestionnaire pour la suppression d'une ligne
    $(document).on('click', '.supprimer-ligne', function() {
        if ($('#tableProduits tbody tr:visible').length > 1) {
            $(this).closest('tr').remove();
            calculerTotaux();
        } else {
            alert('Vous devez avoir au moins un produit dans la vente.');
        }
    });

    // Gestionnaire pour le changement de produit
    $(document).on('change', '.produit-select', function() {
        const ligne = $(this).closest('tr');
        const option = $(this).find('option:selected');
        const prix = option.data('prix');
        const stock = option.data('stock');

        ligne.find('.prix-unitaire').val(prix);
        ligne.find('.quantite').attr('max', stock);
        
        if (parseInt(ligne.find('.quantite').val()) > stock) {
            ligne.find('.quantite').val(stock);
        }

        calculerLigne(ligne);
    });

    // Gestionnaires pour les boutons d'incrémentation/décrémentation
    $(document).on('click', '.incrementer', function() {
        const input = $(this).closest('.input-group').find('.quantite');
        const max = parseInt(input.attr('max'));
        const current = parseInt(input.val());
        if (current < max) {
            input.val(current + 1).trigger('change');
        }
    });

    $(document).on('click', '.decrementer', function() {
        const input = $(this).closest('.input-group').find('.quantite');
        const current = parseInt(input.val());
        if (current > 1) {
            input.val(current - 1).trigger('change');
        }
    });

    // Gestionnaire pour le changement de quantité ou remise
    $(document).on('change', '.quantite, .remise', function() {
        calculerLigne($(this).closest('tr'));
    });

    // Fonction pour calculer le total d'une ligne
    function calculerLigne(ligne) {
        const prix = parseFloat(ligne.find('.prix-unitaire').val()) || 0;
        const quantite = parseInt(ligne.find('.quantite').val()) || 0;
        const remise = parseFloat(ligne.find('.remise').val()) || 0;

        const total = (prix * quantite) - remise;
        ligne.find('.total-ligne').val(total.toFixed(2));
        
        calculerTotaux();
    }

    // Fonction pour calculer les totaux généraux
    function calculerTotaux() {
        let sousTotal = 0;
        let remiseTotale = 0;

        $('.total-ligne').each(function() {
            const ligne = $(this).closest('tr');
            const prix = parseFloat(ligne.find('.prix-unitaire').val()) || 0;
            const quantite = parseInt(ligne.find('.quantite').val()) || 0;
            const remise = parseFloat(ligne.find('.remise').val()) || 0;

            sousTotal += prix * quantite;
            remiseTotale += remise;
        });

        const tva = sousTotal * 0.20;
        const total = sousTotal + tva - remiseTotale;
        const montantPaye = {{ $vente->montant_paye }};
        const resteAPayer = total - montantPaye;

        $('#sousTotal').text(sousTotal.toFixed(2) + ' €');
        $('#montantTva').text(tva.toFixed(2) + ' €');
        $('#montantRemise').text(remiseTotale.toFixed(2) + ' €');
        $('#montantTotal').text(total.toFixed(2) + ' €');
        $('#resteAPayer').text(resteAPayer.toFixed(2) + ' €');
    }

    // Validation du formulaire
    $('#formVente').submit(function(e) {
        if ($('#tableProduits tbody tr:visible').length === 0) {
            e.preventDefault();
            alert('Vous devez avoir au moins un produit dans la vente.');
            return false;
        }

        let isValid = true;
        $('.produit-select').each(function() {
            if (!$(this).val()) {
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez sélectionner un produit pour chaque ligne.');
            return false;
        }

        // Vérifier si le montant total est inférieur au montant déjà payé
        const montantTotal = parseFloat($('#montantTotal').text());
        const montantPaye = {{ $vente->montant_paye }};
        
        if (montantTotal < montantPaye) {
            e.preventDefault();
            alert('Le montant total ne peut pas être inférieur au montant déjà payé (' + montantPaye.toFixed(2) + ' €).');
            return false;
        }
    });

    // Initialiser les calculs au chargement
    calculerTotaux();
});
</script>
@endpush 