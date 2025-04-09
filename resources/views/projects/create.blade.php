@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Créer un nouveau projet</h2>
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
                    
                    <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du projet *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Résumé court du projet *</label>
                            <input type="text" class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" value="{{ old('short_description') }}" maxlength="255" required>
                            @error('short_description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Un court résumé de votre projet (max 255 caractères)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Catégorie *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Sélectionnez une catégorie</option>
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Aucune catégorie disponible</option>
                                @endif
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="region" class="form-label">Région *</label>
                            <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region') }}" required>
                            @error('region')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="funding_goal" class="form-label">Objectif financier (€) *</label>
                            <input type="number" min="1" step="0.01" class="form-control @error('funding_goal') is-invalid @enderror" id="funding_goal" name="funding_goal" value="{{ old('funding_goal') }}" required>
                            @error('funding_goal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Date de fin de la campagne *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée du projet *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Décrivez votre projet en détail : objectifs, impact écologique, utilisation des fonds, etc.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Image principale du projet</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Format recommandé : JPG ou PNG, taille max : 2 Mo</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="media" class="form-label">Images supplémentaires</label>
                            <input type="file" class="form-control @error('media.*') is-invalid @enderror" id="media" name="media[]" accept="image/*" multiple>
                            @error('media.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers (max 5 recommandé). Taille max par fichier : 2 Mo</small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> et je certifie que mon projet respecte la philosophie écologique de Green Spark *
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Créer mon projet</button>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Validation côté client
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            const title = document.getElementById('title').value.trim();
            const shortDescription = document.getElementById('short_description').value.trim();
            const fundingGoal = document.getElementById('funding_goal').value;
            const endDate = document.getElementById('end_date').value;
            const description = document.getElementById('description').value.trim();
            const region = document.getElementById('region').value.trim();
            
            let isValid = true;
            
            // Validation simple côté client
            if (title.length < 5) {
                isValid = false;
                alert('Le titre doit contenir au moins 5 caractères.');
            }
            
            if (shortDescription.length < 10 || shortDescription.length > 255) {
                isValid = false;
                alert('Le résumé court doit contenir entre 10 et 255 caractères.');
            }
            
            if (fundingGoal < 100) {
                isValid = false;
                alert('L\'objectif financier doit être d\'au moins 100€.');
            }
            
            if (description.length < 50) {
                isValid = false;
                alert('La description doit contenir au moins 50 caractères.');
            }
            
            if (region.length < 2) {
                isValid = false;
                alert('Veuillez indiquer une région valide.');
            }
            
            // Vérification de la date de fin (au moins 7 jours dans le futur)
            const today = new Date();
            const endDateObj = new Date(endDate);
            const diffTime = endDateObj - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays < 7) {
                isValid = false;
                alert('La date de fin doit être au moins 7 jours dans le futur.');
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
@endsection