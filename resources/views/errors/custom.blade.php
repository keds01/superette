<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title ?? 'Erreur' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Erreur!</strong>
                        <span class="block sm:inline">{{ $message ?? 'Une erreur est survenue.' }}</span>
                    </div>

                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2">
                            Retour
                        </a>
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            Dashboard
                        </a>
                    </div>

                    @if(config('app.debug') && isset($exception))
                    <div class="mt-8 p-4 bg-gray-100 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Détails de l'erreur (visible uniquement en mode debug)</h3>
                        <div class="overflow-x-auto">
                            <pre class="text-xs">{{ $exception }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
