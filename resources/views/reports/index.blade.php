<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rapports et Analyses') }} (Test Simplifié)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Ceci est un test de la page des rapports. Si vous voyez ce message, la structure de base de la vue fonctionne.
                    Le problème vient probablement du contenu original plus complexe ou des données passées à la vue.
                    Veuillez vérifier le fichier storage/logs/laravel.log pour toute erreur.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>