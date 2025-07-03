@extends('layouts.app')

@section('title', 'Modifier le rôle')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-indigo-800 mb-6 flex items-center">
                <i class="fas fa-user-tag mr-2"></i> Modifier le rôle : {{ $role->name }}
            </h2>

            @if ($errors->any())
    <div class="mb-6">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Erreur(s) :</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ str_replace('The nom field is required.', "Le champ nom du rôle est obligatoire.", $error) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
<form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom du rôle</label>
                        <input type="text" name="name" id="name" value="{{ $role->name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="description" id="description" value="{{ $role->description }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center">
                        <i class="fas fa-key mr-2"></i> Permissions par module
                    </h3>
                    <div class="mb-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="select-all" class="form-checkbox h-5 w-5 text-indigo-600">
                            <span class="ml-2 font-medium text-sm text-gray-700">Tout sélectionner</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="bg-indigo-50 rounded-lg p-4">
                                <h4 class="font-semibold text-indigo-800 mb-2 flex items-center">
                                    <i class="fas fa-layer-group mr-1"></i> {{ $module }}
                                </h4>
                                <ul>
                                    @foreach($permissions as $permission)
                                        <li class="mb-1">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-checkbox h-4 w-4 text-indigo-600 permission-checkbox" {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                <span class="ml-2 text-gray-800 text-sm">{{ $permission->description }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.permission-checkbox');

        if ([...checkboxes].every(cb => cb.checked)) {
            selectAll.checked = true;
        }
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                selectAll.checked = [...checkboxes].every(cb => cb.checked);
            });
        });
    });
</script>
@endpush
