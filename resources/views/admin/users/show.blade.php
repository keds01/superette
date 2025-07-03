@extends('layouts.app')

@section('title', 'Détail utilisateur')

@section('content')
<div class="container py-5">
    <div class="max-w-2xl mx-auto bg-white/70 shadow-xl rounded-xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-indigo-700">Fiche utilisateur</h2>
        <div class="mb-4">
            <strong>Nom :</strong> {{ $user->name }}
        </div>
        <div class="mb-4">
            <strong>Email :</strong> {{ $user->email }}
        </div>
        <div class="mb-4">
            <strong>Statut :</strong>
            <span class="px-2 py-1 rounded-full {{ $user->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $user->actif ? 'Actif' : 'Inactif' }}
            </span>
        </div>
        <div class="mb-4">
            <strong>Rôles :</strong>
            @foreach($user->roles as $role)
                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full mr-1">{{ $role->nom }}</span>
            @endforeach
        </div>
        <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Modifier</a>
        <a href="{{ route('admin.users.index') }}" class="inline-block mt-4 ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Retour</a>
    </div>
</div>
@endsection
