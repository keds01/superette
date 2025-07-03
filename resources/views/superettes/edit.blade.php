@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header moderne -->
            <div class="mb-10 animate-fade-in-down">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-green-400 to-yellow-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Modifier la Superette : {{ $superette->nom }}</h1>
                <p class="mt-2 text-lg text-gray-500">Modifiez les informations de votre superette.</p>
            </div>
            <div class="overflow-hidden rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
                <div class="p-8">

                    @if ($errors->any())
                        <div class="p-4 mb-6 text-red-700 bg-red-100 border border-red-200 rounded-xl">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('superettes.update', $superette) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="code" class="block text-sm font-medium text-blue-700 mb-1">Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $superette->code) }}" required
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Code unique permettant d'identifier la superette</p>
                        </div>

                        <div>
                            <label for="nom" class="block text-sm font-medium text-blue-700 mb-1">Nom</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $superette->nom) }}" required
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">
                        </div>

                        <div>
                            <label for="adresse" class="block text-sm font-medium text-blue-700 mb-1">Adresse</label>
                            <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $superette->adresse) }}"
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">
                        </div>

                        <div>
                            <label for="telephone" class="block text-sm font-medium text-blue-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $superette->telephone) }}"
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-blue-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $superette->email) }}"
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-blue-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="block w-full px-4 py-3 mt-1 border border-blue-200 rounded-lg shadow focus:border-blue-400 focus:ring-2 focus:ring-blue-300 bg-white/70 sm:text-sm">{{ old('description', $superette->description) }}</textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="actif" id="actif" value="1" class="w-4 h-4 text-blue-600 border-blue-300 rounded focus:ring-blue-500" {{ old('actif', $superette->actif) ? 'checked' : '' }}>
                            <label for="actif" class="block ml-2 text-sm font-medium text-blue-700">Actif</label>
                        </div>

                        <div class="flex items-center justify-end gap-2 mt-8">
                            <a href="{{ route('superettes.select') }}" class="inline-flex items-center px-5 py-3 text-sm font-medium text-blue-700 bg-white border border-blue-200 rounded-xl shadow hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex items-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-tr from-indigo-600 to-blue-400 border border-transparent rounded-xl shadow hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 