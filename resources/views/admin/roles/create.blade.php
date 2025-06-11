@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ajouter un nouveau rôle</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du rôle</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">Exemple: admin, gérant, caissier...</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2" required>{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Brève description des responsabilités de ce rôle</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions associées</label>
                            
                            <div class="accordion" id="accordionPermissions">
                                @foreach($permissions_by_module as $module => $modulePermissions)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $module }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse{{ $module }}" aria-expanded="false" 
                                                    aria-controls="collapse{{ $module }}">
                                                {{ $module }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $module }}" class="accordion-collapse collapse" 
                                             aria-labelledby="heading{{ $module }}" data-bs-parent="#accordionPermissions">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach($modulePermissions as $permission)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="permissions[]" value="{{ $permission->id }}" 
                                                                       id="permission-{{ $permission->id }}">
                                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                                    {{ $permission->description }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
