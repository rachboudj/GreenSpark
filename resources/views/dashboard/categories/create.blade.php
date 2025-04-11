@extends('layouts.app')

@section('title', 'Nouvelle catégorie | Green Spark')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-xl-2 bg-light p-3 border-end" style="min-height: calc(100vh - 60px);">
            <h4 class="text-center mb-4">Administration</h4>
            <div class="list-group mb-4">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-grid-1x2"></i> Tableau de bord
                </a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-tags"></i> Catégories
                </a>
                <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-house"></i> Retour au site
                </a>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="col-md-9 col-xl-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Créer une nouvelle catégorie</h1>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="card">
                <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la catégorie *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Le nom doit être unique et contenir au moins 2 caractères.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Une courte description de la catégorie (facultatif).</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection