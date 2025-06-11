@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Journal des activités</h1>
            <div>
                <a href="{{ route('audit.exporter-journal', request()->query()) }}" class="btn-primary">
                    <i class="fas fa-file-export mr-2"></i>Exporter PDF
                </a>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form action="{{ route('audit.journal') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type d'activité</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Tous les types</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date début</label>
                                <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date fin</label>
                                <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('audit.journal') }}" class="btn-secondary">
                            Réinitialiser
                        </a>
                        <button type="submit" class="btn-primary">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tableau des activités -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activites as $activite)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activite->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ in_array($activite->type, ['connexion', 'consultation']) ? 'bg-blue-100 text-blue-800' : 
                              (in_array($activite->type, ['creation', 'modification']) ? 'bg-green-100 text-green-800' : 
                              (in_array($activite->type, ['suppression', 'annulation']) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $activite->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $activite->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activite->user ? $activite->user->name : 'Système' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($activite->model_type && $activite->model_id)
                            <button type="button" class="text-indigo-600 hover:text-indigo-900" 
                                    onclick="showMetadata('{{ json_encode($activite->metadata) }}')">
                                Voir détails
                            </button>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucune activité trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $activites->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher les métadonnées -->
<div id="metadataModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg max-w-lg w-full mx-4">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Détails de l'activité</h3>
            <button type="button" onclick="closeMetadataModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div id="metadataContent" class="overflow-y-auto max-h-96"></div>
        </div>
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <button type="button" onclick="closeMetadataModal()" class="btn-secondary">
                Fermer
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function showMetadata(metadata) {
        const modal = document.getElementById('metadataModal');
        const content = document.getElementById('metadataContent');
        
        try {
            const data = JSON.parse(metadata);
            let html = '<dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">';
            
            for (const [key, value] of Object.entries(data)) {
                html += `
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">${key}</dt>
                    <dd class="mt-1 text-sm text-gray-900">${typeof value === 'object' ? JSON.stringify(value, null, 2) : value}</dd>
                </div>
                `;
            }
            
            html += '</dl>';
            content.innerHTML = html;
        } catch (e) {
            content.innerHTML = '<p class="text-sm text-gray-500">Aucune métadonnée disponible</p>';
        }
        
        modal.classList.remove('hidden');
    }
    
    function closeMetadataModal() {
        const modal = document.getElementById('metadataModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
