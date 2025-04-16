@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mes contributions</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($contributions->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Catégorie</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Statut du projet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contributions as $contribution)
                                <tr>
                                    <td>
                                        <a href="{{ route('projects.show', $contribution->project->slug) }}">
                                            {{ $contribution->project->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $contribution->project->category->name }}</span>
                                    </td>
                                    <td>{{ number_format($contribution->amount, 0, ',', ' ') }} €</td>
                                    <td>{{ $contribution->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($contribution->project->isActive())
                                            <span class="badge bg-success">En cours</span>
                                        @elseif($contribution->project->isGoalReached())
                                            <span class="badge bg-info">Objectif atteint</span>
                                        @elseif($contribution->project->end_date->isPast())
                                            <span class="badge bg-secondary">Terminé</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($contribution->project->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $contribution->project->slug) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Voir le projet
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h4>Total de mes contributions</h4>
            <p class="lead">{{ number_format($contributions->sum('amount'), 0, ',', ' ') }} €</p>
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">Vous n'avez pas encore contribué à un projet.</p>
            <p class="mb-0">
                <a href="{{ route('projects.index') }}">Découvrez les projets écologiques</a> qui pourraient vous intéresser.
            </p>
        </div>
    @endif
</div>
@endsection