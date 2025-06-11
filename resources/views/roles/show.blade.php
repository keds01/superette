@extends('layouts.app')

@section('title', 'Détails du rôle')

@section('content')
<div class="container-fluid py-4">
    <div class="glass-card mb-4">
        <h2 class="section-title">
            <i class="fas fa-user-tag"></i> Détails du rôle: {{ $role->nom }}
        </h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nom:</strong> {{ $role->nom }}
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong> {{ $role->description }}
                        </div>
                        <div>
                            <strong>Nombre d'utilisateurs:</strong> {{ $role->users->count() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Utilisateurs avec ce rôle</h5>
                    </div>
                    <div class="card-body">
                        @if($role->users->isEmpty())
                            <p class="text-muted">Aucun utilisateur n'a ce rôle.</p>
                        @else
                            <div class="list-group">
                                @foreach($role->users as $user)
                                <a href="{{ route('users.show', $user->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-circle">
                                                <span class="initials">{{ substr($user->nom, 0, 1) . substr($user->prenom, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">{{ $user->nom }} {{ $user->prenom }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card mb-4">
            <h3 class="section-title"><i class="fas fa-lock"></i> Permissions accordées</h3>
            
            <div class="row">
                @foreach($permissions_by_module as $module => $permissions)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $module }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($permissions as $permission)
                                <li class="list-group-item {{ $role->permissions->contains($permission->id) ? 'list-group-item-success' : 'list-group-item-light text-muted' }}">
                                    @if($role->permissions->contains($permission->id))
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                    @else
                                        <i class="fas fa-times-circle text-muted me-2"></i>
                                    @endif
                                    {{ $permission->description }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour à la liste
            </a>
            <div>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i> Modifier
                </a>
                @if($role->nom !== 'Administrateur')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
                    <i class="fas fa-trash me-2"></i> Supprimer
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
@if($role->nom !== 'Administrateur')
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le rôle <strong>{{ $role->nom }}</strong> ?</p>
                <p class="text-danger">Cette action est irréversible et retirera ce rôle à tous les utilisateurs qui l'ont.</p>
                @if($role->users->count() > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Ce rôle est actuellement attribué à {{ $role->users->count() }} utilisateur(s).
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #4a6cf7;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .initials {
        font-size: 18px;
        text-transform: uppercase;
    }
</style>
@endpush
