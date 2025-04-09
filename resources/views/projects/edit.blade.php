@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Modifier le projet</h2>
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
                    
                    <form method="POST" action="{{ route('projects.update', $project->slug) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du projet *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $project->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Résumé court du projet *</label>
                            <input type="text" class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" value="{{ old('short_description', $project->short_description) }}" maxlength="255" required>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region', $project->region) }}" required>
                            @error('region')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="funding_goal" class="form-label">Objectif financier (€) *</label>
                            <input type="number" min="1" step="0.01" class="form-control @error('funding_goal') is-invalid @enderror" id="funding_goal" name="funding_goal" value="{{ old('funding_goal', $project->funding_goal) }}" required>
                            @error('funding_goal')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Date de fin de la campagne *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($project->end_date)->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée du projet *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Décrivez votre projet en détail : objectifs, impact écologique, utilisation des fonds, etc.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image principale actuelle</label>
                            @if($project->cover_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $project->cover_image) }}" alt="Image principale" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @else
                                <p class="text-muted">Aucune image principale</p>
                            @endif
                            
                            <label for="cover_image" class="form-label">Changer l'image principale</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Format recommandé : JPG ou PNG, taille max : 2 Mo</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Images supplémentaires actuelles</label>
                            @if($project->media->count() > 0)
                                <div class="row mb-2">
                                    @foreach($project->media as $media)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Image du projet" class="card-img-top">
                                                <div class="card-body p-2">
                                                    <form action="{{ route('projects.media.delete', $media->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger w-100">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucune image supplémentaire</p>
                            @endif
                            
                            <label for="media" class="form-label">Ajouter des images supplémentaires</label>
                            <input type="file" class="form-control @error('media.*') is-invalid @enderror" id="media" name="media[]" accept="image/*" multiple>
                            @error('media.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers. Taille max par fichier : 2 Mo</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <p class="mb-0"><strong>Informations importantes :</strong></p>
                            <ul class="mb-0">
                                <li>Le projet a été démarré le {{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}</li>
                                <li>Montant actuel collecté : {{ number_format($project->current_amount, 2, ',', ' ') }} €</li>
                                <li>Statut actuel : {{ ucfirst($project->status) }}</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Mettre à jour le projet</button>
                            <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-outline-secondary">Annuler</a>
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