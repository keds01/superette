@extends('layouts.app')

@section('title', 'Créer un nouveau rôle')

@section('content')
<div class="container-fluid py-4">
    <div class="glass-card mb-4">
        <h2 class="section-title">
            <i class="fas fa-user-tag"></i> Créer un nouveau rôle
            <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Créez un nouveau rôle avec des permissions spécifiques"></i>
        </h2>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom du rôle</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>
            </div>

            <div class="glass-card mb-4">
                <h3 class="section-title"><i class="fas fa-lock"></i> Permissions</h3>
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label fw-bold" for="select-all">
                                Sélectionner toutes les permissions
                            </label>
                        </div>
                        <hr>
                    </div>
                </div>
                
                <div class="row">
                    @foreach($permissions_by_module as $module => $permissions)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">{{ $module }}</h5>
                            </div>
                            <div class="card-body">
                                @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->description }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gérer la sélection/désélection de toutes les permissions
    $('#select-all').change(function() {
        $('.permission-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Mettre à jour le "select all" si toutes les cases sont cochées manuellement
    $('.permission-checkbox').change(function() {
        if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
            $('#select-all').prop('checked', true);
        } else {
            $('#select-all').prop('checked', false);
        }
    });
});
</script>
@endpush
