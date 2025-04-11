@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Colonne principale avec les infos du projet -->
        <div class="col-lg-8">
            <h1 class="mb-3">{{ $project->title }}</h1>
            
            <div class="d-flex mb-3">
                <span class="badge bg-primary me-2">{{ $project->category->name }}</span>
                <span class="text-muted">Créé par {{ $project->user->name }} {{ $project->created_at->diffForHumans() }}</span>
            </div>
            
            <!-- Image principale -->
            @if($project->cover_image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $project->cover_image) }}" alt="{{ $project->title }}" class="img-fluid rounded" style="height: 60vh; object-fit: cover;">
                </div>
            @endif
            
            <!-- Description du projet -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Description du projet</h3>
                    <p class="lead mb-3">{{ $project->short_description }}</p>
                    <div class="project-description">
                        {!! nl2br(e($project->description)) !!}
                    </div>
                </div>
            </div>
            
            <!-- Galerie d'images -->
            @if($project->media->count() > 0)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Galerie</h3>
                        <div class="row">
                            @foreach($project->media as $media)
                                <div class="col-md-4 mb-3">
                                    <a href="{{ asset('storage/' . $media->file_path) }}" data-lightbox="project-gallery" data-title="{{ $project->title }}">
                                        <img src="{{ asset('storage/' . $media->file_path) }}" alt="Image du projet" class="img-fluid rounded">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Actions du créateur du projet -->
            @if(auth()->check() && auth()->id() === $project->user_id)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Actions du créateur</h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.edit', $project->slug) }}" class="btn btn-primary">Modifier le projet</a>
                            
                            <form action="{{ route('projects.destroy', $project->slug) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer le projet</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Liste des contributions -->
            @if($project->contributions->count() > 0)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Contributions ({{ $project->contributions->count() }})</h3>
                        <ul class="list-group">
                            @foreach($project->contributions->sortByDesc('created_at') as $contribution)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $contribution->user->name }}</strong> a contribué
                                        <span class="badge bg-success">{{ number_format($contribution->amount, 0, ',', ' ') }} €</span>
                                    </div>
                                    <small class="text-muted">{{ $contribution->created_at->diffForHumans() }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Sidebar avec les infos de financement et actions -->
        <div class="col-lg-4">
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-body">
                    <h3 class="card-title">Financement</h3>
                    
                    <div class="d-flex justify-content-between mb-1">
                        <span><strong>{{ number_format($project->current_amount, 0, ',', ' ') }} €</strong> récoltés</span>
                        <span>sur {{ number_format($project->funding_goal, 0, ',', ' ') }} €</span>
                    </div>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $progressPercentage }}%;" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($progressPercentage) }}%</div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <i class="bi bi-people"></i> {{ $project->contributions->count() }} contributeur(s)
                        </div>
                        <div>
                            @if($daysLeft > 0)
                            <i class="bi bi-clock"></i> {{ floor($daysLeft) }} jour{{ floor($daysLeft) > 1 ? 's' : '' }} restant{{ floor($daysLeft) > 1 ? 's' : '' }}
                            @else
                                <i class="bi bi-calendar-check"></i> Campagne terminée
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $project->region }}
                        </small>
                    </div>
                    
                    @if($project->isActive())
                        <div class="d-grid gap-2">
                            <a href="{{ route('contributions.create', $project->slug) }}" class="btn btn-success btn-lg">
                                <i class="bi bi-heart"></i> Contribuer à ce projet
                            </a>
                        </div>
                    @elseif($project->end_date->isPast())
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-info-circle"></i> Cette campagne est terminée
                            @if($project->isGoalReached())
                                <strong>et a atteint son objectif !</strong>
                            @else
                                <strong>sans avoir atteint son objectif.</strong>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-info-circle"></i> Cette campagne n'est plus active.
                        </div>
                    @endif
                    
                    <hr class="my-3">
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="bi bi-share"></i> Partager ce projet
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voir tous les projets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection