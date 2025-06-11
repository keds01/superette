@extends('layouts.app')
@section('title', 'Modifier la permission')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h2 class="text-xl font-bold mb-4 text-indigo-700">Modifier la permission</h2>
    <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nom technique</label>
            <input type="text" name="name" class="form-input mt-1 block w-full" value="{{ $permission->name }}" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <input type="text" name="description" class="form-input mt-1 block w-full" value="{{ $permission->description }}" required>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary mr-2">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
