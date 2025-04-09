@extends('layouts.app')

@section('title', 'Créer un projet | Green Spark')

@section('content')
<div class="container">
    <h1>Lancer un nouveau projet</h1>
    <p class="mb-4">Remplissez le formulaire ci-dessous pour créer votre projet écologique.</p>

    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                Informations générales
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre du projet *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="category_id" class="form-label">Catégorie *</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Sélectionnez une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="short_description" class="form-label">Description courte *</label>
                    <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2" maxlength="255" required>{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maximum 255 caractères.</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description détaillée *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="region" class="form-label">Région *</label>
                    <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region') }}" required>
                    @error('region')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Objectif financier
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="funding_goal" class="form-label">Objectif (€) *</label>
                    <input type="number" class="form-control @error('funding_goal') is-invalid @enderror" id="funding_goal" name="funding_goal" min="1" step="1" value="{{ old('funding_goal') }}" required>
                    @error('funding_goal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Date de début *</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Date de fin *</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Médias
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="cover_image" class="form-label">Image de couverture *</label>
                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*" required>
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="media" class="form-label">Images supplémentaires</label>
                    <input type="file" class="form-control @error('media') is-invalid @enderror" id="media" name="media[]" accept="image/*" multiple>
                    @error('media')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Vous pouvez sélectionner plusieurs fichiers.</div>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('projects.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
            <button type="submit" class="btn btn-success">Créer le projet</button>
        </div>
    </form>
</div>
@endsection