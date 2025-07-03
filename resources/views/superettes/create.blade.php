@extends('layouts.app')

@section('title', 'Créer une Supérette')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Nouvelle Supérette</h1>
                <p class="mt-2 text-lg text-gray-500">Créez une nouvelle supérette en renseignant les informations ci-dessous.</p>
            </div>
            <a href="{{ route('superettes.select') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-2">Veuillez corriger les erreurs ci-dessous&nbsp;:</p>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <form action="{{ route('superettes.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-indigo-700 mb-1">Nom de la supérette <span class="text-red-500">*</span></label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-medium text-indigo-700 mb-1">Code</label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour générer automatiquement un code unique. Ex: SUP001, MAGASIN-CENTRE, etc.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-indigo-700 mb-1">Adresse</label>
                        <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-indigo-700 mb-1">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="email" class="block text-sm font-medium text-indigo-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-indigo-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-lg border border-indigo-200 bg-white/80 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <input type="checkbox" name="actif" id="actif" value="1" class="w-4 h-4 text-indigo-600 border-indigo-300 rounded focus:ring-indigo-500" {{ old('actif', true) ? 'checked' : '' }}>
                    <label for="actif" class="block ml-2 text-sm font-medium text-indigo-700">Actif</label>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 