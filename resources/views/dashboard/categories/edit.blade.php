@extends('layouts.app')

@section('title', 'Modifier la catégorie | Green Spark')

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
                <h1>Modifier la catégorie</h1>
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
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">

                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la catégorie *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Information :</strong> Cette catégorie est utilisée par {{ $category->projects()->count() }} projet(s).
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection