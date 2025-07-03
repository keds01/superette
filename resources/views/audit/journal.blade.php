@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Journal des activités</h1>
                <p class="mt-2 text-lg text-gray-500">Historique détaillé des actions effectuées dans le système</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <a href="{{ route('audit.exporter-journal', request()->query()) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-red-500 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('audit.journal') }}" method="GET">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="type" class="block text-sm font-medium text-indigo-700">Type d'activité</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les types</option>
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-indigo-700">Utilisateur</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-indigo-700">Date début</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}" 
                                   class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-indigo-700">Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}" 
                                   class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('audit.journal') }}" class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Réinitialiser
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-indigo-700 font-medium">Total activités</div>
                <div class="text-3xl font-extrabold text-indigo-900 mt-2">{{ $activites->total() }}</div>
            </div>
            
            <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-green-700 font-medium">Créations</div>
                <div class="text-3xl font-extrabold text-green-900 mt-2">{{ $activites->where('type', 'creation')->count() }}</div>
            </div>
            
            <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-yellow-700 font-medium">Modifications</div>
                <div class="text-3xl font-extrabold text-yellow-900 mt-2">{{ $activites->where('type', 'modification')->count() }}</div>
            </div>
            
            <div class="bg-gradient-to-tr from-red-50 to-red-100 border border-red-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                <div class="text-sm text-red-700 font-medium">Suppressions</div>
                <div class="text-3xl font-extrabold text-red-900 mt-2">{{ $activites->where('type', 'suppression')->count() }}</div>
            </div>
        </div>
        
        <!-- Tableau des activités -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50/80">
                <h3 class="text-lg font-bold text-indigo-800">Liste des activités</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-indigo-100">
                    <thead class="bg-indigo-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Utilisateur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-indigo-50">
                        @forelse($activites as $activite)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $activite->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $activite->type == 'connexion' ? 'bg-blue-100 text-blue-800' : 
                                  ($activite->type == 'creation' ? 'bg-green-100 text-green-800' : 
                                  ($activite->type == 'modification' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($activite->type == 'suppression' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800'))) }}">
                                    {{ ucfirst($activite->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $activite->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $activite->user ? $activite->user->name : 'Système' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($activite->model_type && $activite->model_id)
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-100 text-indigo-700 text-xs font-semibold shadow hover:bg-indigo-200 transition" 
                                        onclick='showMetadata(@json($activite->metadata))'>
                                    <i class="fas fa-eye"></i> Voir détails
                                </button>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-gray-500">Aucune activité trouvée</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-white px-6 py-4 border-t border-indigo-100">
                {{ $activites->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher les métadonnées -->
<div id="metadataModal" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm flex items-center justify-center hidden z-50" tabindex="-1">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 shadow-2xl">
        <div class="px-6 py-4 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-purple-50">
            <h3 class="text-lg font-bold text-indigo-800">Détails de l'activité</h3>
            <button type="button" onclick="closeMetadataModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <span class="sr-only">Fermer</span>
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="px-6 py-6">
            <div id="metadataContent" class="overflow-y-auto max-h-96"></div>
        </div>
        <div class="px-6 py-4 bg-gray-50 text-right border-t border-gray-100 rounded-b-2xl">
            <button type="button" onclick="closeMetadataModal()" class="px-6 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showMetadata(metadata) {
        const modal = document.getElementById('metadataModal');
        const content = document.getElementById('metadataContent');
        
        try {
            let data = metadata;
            if (typeof metadata === 'string') {
                data = JSON.parse(metadata);
            }
            
            let html = '<dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">';
            
            for (const [key, value] of Object.entries(data)) {
                const formattedKey = key.replace(/_/g, ' ')
                    .replace(/\w\S*/g, txt => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
                
                let formattedValue = value;
                if (typeof value === 'object' && value !== null) {
                    formattedValue = '<pre class="bg-gray-50 p-2 rounded overflow-x-auto text-xs">' + 
                        JSON.stringify(value, null, 2) + '</pre>';
                }
                
                html += `
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-indigo-700">${formattedKey}</dt>
                    <dd class="mt-1 text-sm text-gray-700">${formattedValue}</dd>
                </div>
                `;
            }
            
            html += '</dl>';
            content.innerHTML = html;
        } catch (e) {
            content.innerHTML = '<div class="flex flex-col items-center py-8"><i class="fas fa-exclamation-circle text-gray-400 text-2xl mb-2"></i><p class="text-gray-500">Aucune métadonnée disponible</p></div>';
        }
        
        modal.classList.remove('hidden');
        
        // Ajouter un gestionnaire d'événement pour fermer la modal en cliquant à l'extérieur
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeMetadataModal();
            }
        });
    }
    
    function closeMetadataModal() {
        const modal = document.getElementById('metadataModal');
        modal.classList.add('hidden');
    }
</script>
@endpush
