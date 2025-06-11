@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($document) ? 'Modifier le Document' : 'Ajouter un Document' }}
            </h1>
            <a href="{{ isset($document) ? route('documents-employes.show', $document) : route('documents-employes.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ isset($document) ? route('documents-employes.update', $document) : route('documents-employes.store', $employe) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @if(isset($document))
                    @method('PUT')
                @endif

                <!-- Informations de l'employé -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">Employé</h2>
                    <p class="text-gray-600">
                        {{ isset($document) ? $document->employe->nom . ' ' . $document->employe->prenom : $employe->nom . ' ' . $employe->prenom }}
                    </p>
                </div>

                <!-- Type de document -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type de Document <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="">Sélectionnez un type</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ (isset($document) && $document->type == $value) || old('type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Titre -->
                <div>
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titre" id="titre" required
                           value="{{ isset($document) ? $document->titre : old('titre') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                           placeholder="Ex: Contrat de travail 2024">
                    @error('titre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fichier -->
                <div>
                    <label for="fichier" class="block text-sm font-medium text-gray-700 mb-1">
                        Fichier {{ !isset($document) ? '<span class="text-red-500">*</span>' : '' }}
                    </label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="fichier" id="fichier" {{ !isset($document) ? 'required' : '' }}
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    @if(isset($document))
                        <p class="mt-2 text-sm text-gray-500">
                            Fichier actuel : {{ $document->fichier }}
                            <br>
                            <span class="text-xs">Laissez vide pour conserver le fichier actuel</span>
                        </p>
                    @endif
                    @error('fichier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date du document -->
                <div>
                    <label for="date_document" class="block text-sm font-medium text-gray-700 mb-1">
                        Date du Document <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_document" id="date_document" required
                           value="{{ isset($document) ? $document->date_document->format('Y-m-d') : old('date_document') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    @error('date_document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date d'expiration -->
                <div>
                    <label for="date_expiration" class="block text-sm font-medium text-gray-700 mb-1">
                        Date d'Expiration
                    </label>
                    <input type="date" name="date_expiration" id="date_expiration"
                           value="{{ isset($document) && $document->date_expiration ? $document->date_expiration->format('Y-m-d') : old('date_expiration') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <p class="mt-1 text-sm text-gray-500">
                        Laissez vide si le document n'a pas de date d'expiration
                    </p>
                    @error('date_expiration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confidentialité -->
                <div class="flex items-center">
                    <input type="checkbox" name="est_confidentiel" id="est_confidentiel" value="1"
                           {{ (isset($document) && $document->est_confidentiel) || old('est_confidentiel') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="est_confidentiel" class="ml-2 block text-sm text-gray-700">
                        Document confidentiel
                    </label>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                              placeholder="Description détaillée du document...">{{ isset($document) ? $document->description : old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ isset($document) ? route('documents-employes.show', $document) : route('documents-employes.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        {{ isset($document) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Validation de la date d'expiration
    document.getElementById('date_document').addEventListener('change', function() {
        const dateDocument = new Date(this.value);
        const dateExpiration = document.getElementById('date_expiration');
        
        if (dateExpiration.value) {
            const dateExp = new Date(dateExpiration.value);
            if (dateExp < dateDocument) {
                alert('La date d\'expiration doit être postérieure à la date du document');
                dateExpiration.value = '';
            }
        }
    });

    // Validation du fichier
    document.getElementById('fichier').addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];

        if (file) {
            if (file.size > maxSize) {
                alert('Le fichier ne doit pas dépasser 10MB');
                this.value = '';
            }
            if (!allowedTypes.includes(file.type)) {
                alert('Format de fichier non autorisé. Formats acceptés : PDF, DOC, DOCX, JPG, PNG');
                this.value = '';
            }
        }
    });
</script>
@endpush
@endsection 