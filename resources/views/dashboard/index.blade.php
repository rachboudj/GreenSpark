@extends('layouts.app')

@section('title', 'Tableau de bord administrateur | Green Spark')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-xl-2 bg-light p-3 border-end" style="min-height: calc(100vh - 60px);">
            <h4 class="text-center mb-4">Administration</h4>
            <div class="list-group mb-4">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-grid-1x2"></i> Tableau de bord
                </a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-tags"></i> Catégories
                </a>
                <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-house"></i> Retour au site
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    Statistiques
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Projets
                            <span class="badge bg-primary rounded-pill">{{ $stats['total_projects'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Projets actifs
                            <span class="badge bg-success rounded-pill">{{ $stats['active_projects'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Utilisateurs
                            <span class="badge bg-info rounded-pill">{{ $stats['total_users'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Contributions
                            <span class="badge bg-warning rounded-pill">{{ number_format($stats['total_contributions'], 2, ',', ' ') }} €</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="col-md-9 col-xl-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Gestion des projets</h1>
                <div>
                    <a href="{{ route('projects.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Nouveau projet
                    </a>
                </div>
            </div>
            
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Tous les projets</span>
                        <input type="text" class="form-control form-control-sm w-25" id="projectSearch" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Utilisateur</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Date de création</th>
                                    <th>Montant</th>
                                    <th>Objectif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="projectsTable">
                                @forelse ($projects as $project)
                                    <tr>
                                        <td>{{ $project->id }}</td>
                                        <td>
                                            <a href="{{ route('projects.show', $project->slug) }}" target="_blank">
                                                {{ $project->title }}
                                            </a>
                                        </td>
                                        <td>{{ $project->user->name }}</td>
                                        <td>{{ $project->category->name }}</td>
                                        <td>
                                            <form action="{{ route('admin.update-project-status', $project->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="draft" {{ $project->status == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                    <option value="pending" {{ $project->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                                    <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Actif</option>
                                                    <option value="funded" {{ $project->status == 'funded' ? 'selected' : '' }}>Financé</option>
                                                    <option value="closed" {{ $project->status == 'closed' ? 'selected' : '' }}>Fermé</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ number_format($project->current_amount, 2, ',', ' ') }} €</td>
                                        <td>{{ number_format($project->funding_goal, 2, ',', ' ') }} €</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                    <i class="bi bi-eye"></i> Voir
                                                </a>
                                                <a href="{{ route('projects.edit', $project->slug) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                @if($project->contributions->count() == 0)
                                                    <form action="{{ route('admin.delete-project', $project->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-danger" disabled title="Impossible de supprimer (contributions)">
                                                        <i class="bi bi-trash"></i> Impossible de supprimer (contributions)
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucun projet trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Recherche en temps réel
        const searchInput = document.getElementById('projectSearch');
        const table = document.getElementById('projectsTable');
        const rows = table.getElementsByTagName('tr');
        
        searchInput.addEventListener('keyup', function () {
            const searchTerm = searchInput.value.toLowerCase();
            
            for (let i = 0; i < rows.length; i++) {
                const rowText = rows[i].textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });
</script>
@endsection