@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-indigo-900">Nouvelle Remise</h2>
                    <p class="mt-2 text-gray-600">Créez une remise sur une vente existante. Les remises permettent d'appliquer une réduction à une vente sélectionnée.</p>
                </div>
                <div>
                    <a href="{{ route('remises.index') }}" class="px-6 py-3 rounded-xl border border-indigo-200 text-indigo-600 hover:bg-indigo-50 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow animate-fade-in" role="alert">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                        <span class="font-bold">Erreur(s) :</span>
                    </div>
                    <ul class="list-disc pl-6 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow animate-fade-in" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            @if(isset($vente))
                @include('remises.form', ['vente' => $vente])
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Aucune vente sélectionnée. Vous allez être redirigé vers la sélection de vente...
                            </p>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "{{ route('remises.select-vente') }}";
                    }, 1500);
                </script>
            @endif
        </div>
    </div>
</div>
@endsection 