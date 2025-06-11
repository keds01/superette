<!-- Bloc statistiques avancées, badges de statut et logs pour une commande -->
<div class="space-y-6">
    <!-- Statistiques avancées -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 0h-4m4 0a2 2 0 01-2 2h-4a2 2 0 01-2-2m8 0V7a2 2 0 00-2-2H8a2 2 0 00-2 2v10" />
                </svg>
                Statistiques de la commande
            </h3>
            <ul class="space-y-2">
                <li class="flex justify-between text-gray-700">
                    <span>Nombre de produits</span>
                    <span class="font-semibold">{{ $commande->details->count() }}</span>
                </li>
                <li class="flex justify-between text-gray-700">
                    <span>Quantité totale</span>
                    <span class="font-semibold">
                        {{ number_format($commande->details->sum('quantite'), 2, ',', ' ') }}
                    </span>
                </li>
                <li class="flex justify-between text-gray-700">
                    <span>Montant total</span>
                    <span class="font-semibold">
                        {{ number_format($commande->montant_total, 2, ',', ' ') }} {{ $commande->devise }}
                    </span>
                </li>
                <li class="flex justify-between text-gray-700">
                    <span>Fournisseur</span>
                    <span class="font-semibold text-indigo-600">{{ $commande->fournisseur->nom ?? '-' }}</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- Badges de statut avancés -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                </svg>
                Statut avancé
            </h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $statusColors = [
                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                        'en_cours' => 'bg-blue-100 text-blue-800',
                        'livree' => 'bg-green-100 text-green-800',
                        'annulee' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-block px-3 py-1 rounded-xl {{ $statusColors[$commande->statut] ?? 'bg-gray-100 text-gray-800' }} font-semibold">
                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                </span>
                @if($commande->date_livraison_prevue < now() && $commande->statut !== 'livree')
                    <span class="inline-block px-3 py-1 rounded-xl bg-red-100 text-red-800 font-semibold">
                        Retard de livraison
                    </span>
                @endif
            </div>
        </div>
    </div>
    <!-- Bloc logs / historique -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                </svg>
                Historique & Logs
            </h3>
            <ul class="divide-y divide-gray-100">
                @php
                $logs = App\Models\ActivityLog::where('model_type', 'App\\Models\\Commande')
                    ->where('model_id', $commande->id)
                    ->latest()
                    ->take(10)
                    ->get();
                @endphp
                @forelse($logs as $log)
                    <li class="py-2 flex flex-col">
                        <span class="text-sm text-gray-700">{{ $log->created_at->format('d/m/Y H:i') }}
                            <span class="mx-2">•</span>
                            <span class="font-semibold">{{ $log->type }}</span>
                        </span>
                        <span class="text-gray-500">{{ $log->description }}</span>
                        <span class="text-xs text-gray-400">Par {{ $log->user->name ?? 'Système' }}</span>
                    </li>
                @empty
                    <li class="py-2 text-gray-400">Aucune activité récente pour cette commande.</li>
                @endforelse
            </ul>
            <div class="mt-4 text-right">
                <a href="{{ route('audit.journal', ['model_type' => 'App\\Models\\Commande', 'model_id' => $commande->id]) }}"
                   class="text-indigo-600 hover:underline text-sm font-semibold">
                    Voir tout l'historique
                </a>
            </div>
        </div>
    </div>
</div>
