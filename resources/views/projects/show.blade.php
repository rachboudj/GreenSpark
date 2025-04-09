@extends('layouts.app')

@section('title', $project->title . ' | Green Spark')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @if($project->cover_image)
                <img src="{{ asset('storage/' . $project->cover_image) }}" class="img-fluid rounded mb-4" alt="{{ $project->title }}">
            @endif

            <h1>{{ $project->title }}</h1>
            
            <div class="d-flex mb-3">
                <span class="badge bg-secondary me-2">{{ $project->category->name }}</span>
                <span class="badge bg-info me-2">{{ $project->region }}</span>
                <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'funded' ? 'primary' : 'secondary') }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            <div class="mb-4">
                <h5>Description</h5>
                <p>{{ $project->short_description }}</p>
                <div class="mt-3">
                    {!! nl2br(e($project->description)) !!}
                </div>
            </div>

            @if($project->media->count() > 0)
                <div class="mb-4">
                    <h5>Galerie média</h5>
                    <div class="row">
                        @foreach($project->media as $media)
                            <div class="col-md-4 mb-3">
                                @if($media->media_type == 'image')
                                    <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded" alt="Image du projet">
                                @elseif($media->media_type == 'video')
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $media->file_path }}" allowfullscreen></iframe>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <h5>Créateur du projet</h5>
                <p>{{ $project->user->name }}</p>
            </div>

            <div class="mb-4">
                <h5>Période de financement</h5>
                <p>Du {{ $project->start_date->format('d/m/Y') }} au {{ $project->end_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Financement</h5>
                    
                    <div class="progress mb-3">
                        @php $percentage = ($project->current_amount / $project->funding_goal) * 100; @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 0) }}%
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span><strong>{{ number_format($project->current_amount, 0) }} €</strong> récoltés</span>
                        <span>Objectif: {{ number_format($project->funding_goal, 0) }} €</span>
                    </div>

                    <p>Soutenu par {{ $project->contributions->count() }} contributeurs</p>
                    
                    @if($project->status === 'active')
                        <p>
                            @php 
                                $daysLeft = now()->diffInDays($project->end_date, false);
                            @endphp
                            @if($daysLeft > 0)
                                <strong>{{ $daysLeft }}</strong> jours restants
                            @elseif($daysLeft == 0)
                                <strong>Dernier jour</strong> pour contribuer
                            @else
                                Campagne terminée
                            @endif
                        </p>

                        @if($daysLeft >= 0)
                            <form action="{{ route('contributions.store', $project->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Montant de la contribution (€)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="1" step="1" required>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success w-100">Contribuer</button>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-secondary">
                            Ce projet n'accepte plus de contributions.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Partager ce projet</h5>
                    <div class="d-flex justify-content-around mt-3">
                        <a href="#" class="btn btn-outline-primary"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-info"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="btn btn-outline-success"><i class="bi bi-envelope"></i></a>
                        <a href="#" class="btn btn-outline-dark"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection