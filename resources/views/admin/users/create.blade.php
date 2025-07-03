@extends('layouts.app')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-purple-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Ajouter un utilisateur</h1>
            <p class="mt-2 text-lg text-gray-500">Créez un nouveau compte utilisateur pour la plateforme.</p>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-700 rounded-xl px-4 py-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                    <div class="relative">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-3 text-gray-900 shadow-sm placeholder-gray-400">
                        <span class="absolute right-3 top-3.5 text-indigo-400">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-3 text-gray-900 shadow-sm placeholder-gray-400">
                        <span class="absolute right-3 top-3.5 text-indigo-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="superette_id" class="block text-sm font-medium text-gray-700 mb-1">Superette</label>
                    <div class="relative">
                        <select name="superette_id" id="superette_id" 
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-3 text-gray-900 shadow-sm">
                            <option value="">Aucune (Super Admin)</option>
                            @foreach(App\Models\Superette::orderBy('nom')->get() as $superette)
                                <option value="{{ $superette->id }}" {{ old('superette_id') == $superette->id ? 'selected' : '' }}>
                                    {{ $superette->nom }} ({{ $superette->code }})
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute right-3 top-3.5 text-indigo-400">
                            <i class="fas fa-store"></i>
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Laisser vide uniquement pour les Super Administrateurs. Les utilisateurs standards doivent être affectés à une superette.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-3 text-gray-900 shadow-sm placeholder-gray-400">
                            <span class="absolute right-3 top-3.5 text-indigo-400">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-4 py-3 text-gray-900 shadow-sm placeholder-gray-400">
                            <span class="absolute right-3 top-3.5 text-indigo-400">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôles</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach($roles as $role)
                            @if($role->name !== 'gestionnaire')
                                <label class="inline-flex items-center px-3 py-2 bg-indigo-50 rounded-xl shadow-sm cursor-pointer hover:bg-indigo-100">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-checkbox text-indigo-600 mr-2">
                                    <span class="text-indigo-700 font-medium">{{ $role->name }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-4 mt-6">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-user-plus"></i>
                        Enregistrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold shadow hover:bg-gray-200 transition-all duration-200">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
