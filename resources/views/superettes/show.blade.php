@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Header moderne -->
            <div class="mb-10 animate-fade-in-down">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-green-400 to-yellow-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Détails de la Superette : {{ $superette->nom }}</h1>
                <p class="mt-2 text-lg text-gray-500">Toutes les informations et statistiques de cette superette.</p>
            </div>
            <div class="overflow-hidden rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
                <div class="p-8">
                    @if (session('success'))
                        <div class="p-4 mb-6 text-green-700 bg-green-100 border border-green-200 rounded-xl">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                        <div class="flex gap-2">
                            <a href="{{ route('superettes.edit', $superette) }}" class="inline-flex items-center px-5 py-3 text-sm font-bold text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-xl shadow hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition">
                                <i class="fas fa-edit mr-2"></i> Modifier
                            </a>
                            <form action="{{ route('superettes.activate', $superette->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-5 py-3 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl shadow hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 transition">
                                    <i class="fas fa-check-circle mr-2"></i> Sélectionner
                                </button>
                            </form>
                        </div>
                        <a href="{{ route('superettes.select') }}" class="inline-flex items-center px-5 py-3 text-sm font-medium text-blue-700 bg-white border border-blue-200 rounded-xl shadow hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                        </a>
                    </div>

                    <!-- Infos générales -->
                    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Code :</span>
                                <span class="font-bold text-blue-900">{{ $superette->code }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Statut :</span>
                                @if ($superette->actif)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300 ml-2">Actif</span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300 ml-2">Inactif</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Adresse :</span>
                                <span class="text-gray-900 ml-2">{{ $superette->adresse ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Téléphone :</span>
                                <span class="text-gray-900 ml-2">{{ $superette->telephone ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Email :</span>
                                <span class="text-gray-900 ml-2">{{ $superette->email ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-blue-700 font-medium">Créée le :</span>
                                <span class="text-gray-900 ml-2">{{ $superette->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        @if($superette->description)
                        <div>
                            <span class="text-sm text-blue-700 font-medium">Description :</span>
                            <div class="p-4 mt-2 bg-blue-50 rounded-xl text-gray-700 shadow-inner">{{ $superette->description }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Statistiques -->
                    <div class="mb-10">
                        <h3 class="mb-4 text-lg font-semibold text-blue-700">Statistiques</h3>
                        <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                            <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                                <h3 class="text-sm font-medium text-blue-700">Utilisateurs</h3>
                                <p class="text-3xl font-bold text-blue-900 mt-2">{{ isset($users) ? count($users) : 0 }}</p>
                            </div>
                            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                                <h3 class="text-sm font-medium text-green-700">Produits</h3>
                                <p class="text-3xl font-bold text-green-900 mt-2">{{ $nbProduits }}</p>
                            </div>
                            <div class="relative bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                                <h3 class="text-sm font-medium text-yellow-700">Clients</h3>
                                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $nbClients }}</p>
                            </div>
                            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                                <h3 class="text-sm font-medium text-purple-700">Ventes</h3>
                                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $nbVentes }}</p>
                            </div>
                        </div>
                    </div>

                    @if(count($users) > 0)
                        <div class="mt-8">
                            <h3 class="mb-4 text-lg font-semibold text-blue-700">Utilisateurs associés</h3>
                            <div class="overflow-x-auto rounded-2xl shadow bg-white/80 border border-blue-100">
                                <table class="min-w-full divide-y divide-blue-200">
                                    <thead class="bg-gradient-to-tr from-blue-100 to-green-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Rôle</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white/80 divide-y divide-blue-50">
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-blue-900">{{ $user->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $user->email }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($user->role)
                                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300 mr-1">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">Non défini</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($user->actif)
                                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">Actif</span>
                                                    @else
                                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">Inactif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection