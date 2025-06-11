@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détail de la remise (Admin)</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center gap-2">
                <i class="fas fa-percentage"></i> Remise #{{ $remise->id }}
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="font-semibold text-gray-700">Type</dt>
                    <dd class="text-gray-900">{{ $remise->type_remise === 'pourcentage' ? 'Pourcentage' : 'Montant fixe' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Valeur</dt>
                    <dd class="text-gray-900">
                        @if($remise->type_remise === 'pourcentage')
                            {{ $remise->valeur_remise }} %
                        @else
                            {{ number_format($remise->valeur_remise, 0, ',', ' ') }} FCFA
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Code</dt>
                    <dd class="text-gray-900">{{ $remise->code_remise ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Description</dt>
                    <dd class="text-gray-900">{{ $remise->description ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Créée le</dt>
                    <dd class="text-gray-900">{{ $remise->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Vente associée</dt>
                    <dd class="text-gray-900">
                        @if($remise->vente)
                            <a href="{{ route('ventes.show', $remise->vente) }}" class="text-blue-600 hover:underline">Vente #{{ $remise->vente->id }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
            <div class="mt-8 flex gap-4">
                <a href="{{ route('admin.remises.edit', $remise) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <form action="{{ route('admin.remises.destroy', $remise) }}" method="POST" onsubmit="return confirm('Supprimer cette remise ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('admin.remises.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
