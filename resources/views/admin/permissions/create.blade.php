@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-purple text-white" style="background-color: #8a2be2;">
                    <h5 class="mb-0">Ajouter une nouvelle permission</h5>
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

                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom technique</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">Format recommandé: module.action (ex: produits.creer)</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" required>
                            <small class="form-text text-muted">Description claire de ce que permet cette permission</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" style="background-color: #8a2be2; border-color: #8a2be2;">Enregistrer</button>
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
