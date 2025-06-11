@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <!-- En-tête -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-indigo-900">Détails de l'alerte ({{ $alertTypes[$alert->type] }})</h2>
                    <p class="mt-2 text-gray-600">Informations détaillées sur cette alerte.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('alertes.edit', $alert) }}" 
                       class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        Modifier
                    </a>
                    <form action="{{ route('alertes.destroy', $alert) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition-colors"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informations de l'alerte -->
            <div class="bg-indigo-50 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-medium text-indigo-900 mb-4">Paramètres de l'alerte</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-800">
                    <div>
                        <p><span class="font-medium text-indigo-700">Type :</span> {{ $alertTypes[$alert->type] }}</p>
                        <p class="mt-2"><span class="font-medium text-indigo-700">Catégorie :</span> {{ $alert->category ? $alert->categorie->nom : 'Toutes les catégories' }}</p>
                         <p class="mt-2"><span class="font-medium text-indigo-700">Statut :</span> 
                            <span @class([
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-800' => $alert->actif,
                                'bg-gray-100 text-gray-800' => !$alert->actif
                            ])>
                                {{ $alert->actif ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p><span class="font-medium text-indigo-700">Seuil :</span> {{ number_format($alert->seuil, 2) }}</p>
                        @if(in_array($alert->type, ['peremption', 'mouvement_important']))
                             <p class="mt-2"><span class="font-medium text-indigo-700">Période :</span> {{ $alert->periode }} jours</p>
                        @endif
                        <p class="mt-2"><span class="font-medium text-indigo-700">Email de notification :</span> {{ $alert->notification_email ?: 'Non configuré' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-gray-100 rounded-xl p-6">
                 <h3 class="text-lg font-medium text-gray-900 mb-4">Informations système</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-800">
                     <div>
                         <p><span class="font-medium text-gray-700">Créée le :</span> {{ $alert->created_at->format('d/m/Y H:i') }}</p>
                         @if($alert->created_at != $alert->updated_at)
                             <p class="mt-2"><span class="font-medium text-gray-700">Dernière modification :</span> {{ $alert->updated_at->format('d/m/Y H:i') }}</p>
                         @endif
                     </div>
                      @if($alert->user)
                        <div>
                            <p><span class="font-medium text-gray-700">Créée par :</span> {{ $alert->user->name }}</p>
                            {{-- Ajouter d'autres infos utilisateur si besoin --}}
                        </div>
                    @endif
                 </div>
             </div>

            <!-- Bouton Retour -->
            <div class="flex justify-start mt-8">
                <a href="{{ route('alertes.index') }}" 
                   class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors">
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 