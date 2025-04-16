@extends('layouts.app')

@section('title', 'Tous les projets | Green Spark')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tous les projets</h1>
        @auth
            <a href="{{ route('projects.create') }}" class="btn btn-success">Lancer un projet</a>
        @endauth
    </div>

    <div class="row mb-4">
        <form action="{{ route('projects.index') }}" method="GET" class="row">
            <div class="col-md-8">
                <div class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Rechercher un projet..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">Rechercher</button>
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="row">
        @forelse($projects as $project)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                @if($project->cover_image)
                    <img src="{{ asset('storage/' . $project->cover_image) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-light text-center p-5" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                    </div>
                @endif
                    <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-secondary">{{ $project->category->name }}</span>
                        @if($project->status == 'funded')
                            <span class="badge bg-success">Financé</span>
                        @elseif($project->status == 'active')
                            <span class="badge bg-primary">Actif</span>
                        @endif
                    </div>
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
                        <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-sm btn-primary">Voir le projet</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p>Aucun projet trouvé.</p>
                @auth
                    <a href="{{ route('projects.create') }}" class="btn btn-success">Lancez le premier projet !</a>
                @else
                    <p>Connectez-vous pour lancer un projet.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">Se connecter</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">S'inscrire</a>
                @endauth
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $projects->appends(request()->query())->links() }}
    </div>
</div>
@endsection