@extends('layouts.app')

@section('title', 'Green Spark - Financement participatif pour projets écologiques')

@section('content')
<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold text-body-emphasis">Green Spark</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">Plateforme de financement participatif pour des projets écologiques et durables.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="{{ route('projects.index') }}" class="btn btn-primary btn-lg px-4 gap-3">Découvrir les projets</a>
            @auth
                <a href="{{ route('projects.create') }}" class="btn btn-outline-success btn-lg px-4">Lancer mon projet</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">Rejoindre la communauté</a>
            @endauth
        </div>
    </div>
</div>

<div class="container">
    <h2 class="text-center mb-4">Projets en vedette</h2>
    <div class="row">
        @foreach($projects as $project)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($project->cover_image)
                        <img src="{{ asset('storage/' . $project->cover_image) }}" class="card-img-top" alt="{{ $project->title }}">
                    @else
                        <div class="bg-light text-center p-5">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $project->title }}</h5>
                        <p class="card-text">{{ $project->short_description }}</p>
                        
                        <div class="progress mb-3">
                            @php $percentage = ($project->current_amount / $project->funding_goal) * 100; @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                {{ number_format($percentage, 0) }}%
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <span><strong>{{ number_format($project->current_amount, 0) }} €</strong> récoltés</span>
                            <span>Objectif: {{ number_format($project->funding_goal, 0) }} €</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">Voir le projet</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">Voir tous les projets</a>
    </div>
</div>
@endsection