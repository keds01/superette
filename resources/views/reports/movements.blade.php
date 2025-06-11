<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Mouvements de stock</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Rapport détaillé des mouvements de stock avec filtres par période et catégorie.
                    </p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="{{ route('reports.export', [
                        'type' => 'movements',
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]) }}" class="btn-secondary">
                        Exporter en CSV
                    </a>
                </div>
            </div>

            <!-- Filtres -->
            <div class="mt-8 bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('reports.movements') }}" method="GET" class="space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                        <div class="sm:w-64">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" name="start_date" id="start_date" class="form-input mt-1 block w-full" value="{{ $startDate }}">
                        </div>

                        <div class="sm:w-64">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" name="end_date" id="end_date" class="form-input mt-1 block w-full" value="{{ $endDate }}">
                        </div>

                        <div class="sm:w-64">
                            <label for="type" class="block text-sm font-medium text-gray-700">Type de mouvement</label>
                            <select name="type" id="type" class="form-input mt-1 block w-full">
                                <option value="">Tous les types</option>
                                <option value="entree" @selected($type === 'entree')>Entrées</option>
                                <option value="sortie" @selected($type === 'sortie')>Sorties</option>
                            </select>
                        </div>

                        <div class="sm:w-64">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select name="category_id" id="category_id" class="form-input mt-1 block w-full">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected($categoryId == $category->id)>
                                        {{ $category->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:flex sm:items-end">
                            <button type="submit" class="btn-primary mt-3 sm:mt-0">
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résumé -->
            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total entrées</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totals->total_entrees, 0, ',', ' ') }} FCFA</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total sorties</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totals->total_sorties, 0, ',', ' ') }} FCFA</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Quantité entrées</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totals->quantite_entrees, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Quantité sorties</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totals->quantite_sorties, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des mouvements -->
            <div class="mt-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Produit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantité</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Prix unitaire</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Montant total</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Motif</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date péremption</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($movements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movement->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        <div class="font-medium">{{ $movement->product->nom }}</div>
                                        <div class="text-gray-500">{{ $movement->product->categorie->nom }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($movement->type === 'entree')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                Entrée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                Sortie
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ number_format($movement->quantite, 2) }} {{ $movement->product->unit->symbole }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ number_format($movement->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ number_format($movement->quantite * $movement->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ Str::limit($movement->motif, 50) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($movement->date_peremption)
                                            <span @class([
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                'bg-yellow-100 text-yellow-800' => $movement->date_peremption->diffInDays(now()) <= 30,
                                                'bg-gray-100 text-gray-800' => $movement->date_peremption->diffInDays(now()) > 30
                                            ])>
                                                {{ $movement->date_peremption->format('d/m/Y') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-sm text-gray-500 text-center">
                                        Aucun mouvement trouvé pour cette période
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 