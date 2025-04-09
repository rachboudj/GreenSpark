@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mes projets</h1>
    
    <div class="mb-4">
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Créer un nouveau projet
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($projects->count() > 0)
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        @if($project->thumbnail)
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->title }}</h5>
                            <span class="badge bg-primary mb-2">{{ $project->category->name }}</span>
                            
                            <div class="mb-2">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $project->fundingPercentage() }}%;" aria-valuenow="{{ $project->fundingPercentage() }}" aria-valuemin="0" aria-valuemax="100">{{ round($project->fundingPercentage()) }}%</div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small>{{ number_format($project->current_amount, 0, ',', ' ') }} €</small>
                                    <small>Objectif: {{ number_format($project->goal_amount, 0, ',', ' ') }} €</small>
                                </div>
                            </div>
                            
                            <p class="card-text text-muted">
                                @if($project->isActive())
                                    <i class="bi bi-clock"></i> {{ $project->daysLeft() }} jours restants
                                @elseif($project->end_date->isPast())
                                    <i class="bi bi-calendar-check"></i> Campagne terminée
                                @else
                                    <i class="bi bi-info-circle"></i> {{ ucfirst($project->status) }}
                                @endif
                            </p>
                            
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($project->description, 100) }}</p>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <a href="{{ route('projects.edit', $project->slug) }}" class="btn btn-outline-secondary flex-grow-1">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">Vous n'avez pas encore créé de projet.</p>
            <p class="mb-0">
                <a href="{{ route('projects.create') }}">Créer votre premier projet écologique</a> pour commencer à recevoir des financements.
            </p>
        </div>
    @endif
</div>
@endsection