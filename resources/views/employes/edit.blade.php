@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-2xl p-8 md:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-violet-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Modifier l'employé
                </h1>
                <p class="mt-3 text-gray-600">Modifiez les informations de l'employé puis validez.</p>
            </div>

            <form action="{{ route('employes.update', $employe->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Nom de famille de l'employé"></i></label>
                        <input type="text" 
                               name="nom" 
                               id="nom" 
                               value="{{ old('nom', $employe->nom) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm"
                               required>
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Prénom de l'employé"></i></label>
                        <input type="text" 
                               name="prenom" 
                               id="prenom" 
                               value="{{ old('prenom', $employe->prenom) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm"
                               required>
                        @error('prenom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Adresse email professionnelle"></i></label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $employe->email) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Numéro de téléphone de contact"></i></label>
                        <input type="tel" 
                               name="telephone" 
                               id="telephone" 
                               value="{{ old('telephone', $employe->telephone) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm">
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Adresse de résidence"></i></label>
                        <input type="text" 
                               name="adresse" 
                               id="adresse" 
                               value="{{ old('adresse', $employe->adresse) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm">
                        @error('adresse')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div></div>
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Rôle <span class="text-red-500">*</span> <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Rôle de l'employé dans le système"></i></label>
                    <select name="role_id" 
                            id="role_id"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm"
                            required>
                        <option value="">Sélectionnez un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $employe->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="actif" class="block text-sm font-medium text-gray-700">Statut <i class="fas fa-info-circle text-gray-400" data-bs-toggle="tooltip" title="Indique si l'employé est actif ou inactif"></i></label>
                    <select name="actif" 
                            id="actif"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50 shadow-sm">
                        <option value="1" {{ old('actif', $employe->actif) == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ old('actif', $employe->actif) == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('actif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <a href="{{ route('employes.index') }}" 
                       class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-violet-600 via-indigo-500 to-purple-600 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Enregistrer les modifications
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
        // Activation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
