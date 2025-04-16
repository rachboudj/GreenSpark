@extends('layouts.app')

@section('title', 'Gestion des catégories | Green Spark')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-xl-2 bg-light p-3 border-end" style="min-height: calc(100vh - 60px);">
            <h4 class="text-center mb-4">Administration</h4>
            <div class="list-group mb-4">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-grid-1x2"></i> Tableau de bord
                </a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
                <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Catégories
                </a>
                <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-house"></i> Retour au site
                </a>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="col-md-9 col-xl-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Gestion des catégories</h1>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nouvelle catégorie
                </a>
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
                        <span>Toutes les catégories</span>
                        <input type="text" class="form-control form-control-sm w-25" id="categorySearch" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Slug</th>
                                    <th>Description</th>
                                    <th>Projets</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTable">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td>{{ Str::limit($category->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $category->projects()->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                @if($category->projects()->count() == 0)
                                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?');">
                                                @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-danger" disabled title="Impossible de supprimer (utilisée par des projets)">
                                                        <i class="bi bi-trash">Impossible de supprimer (utilisée par des projets)</i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune catégorie trouvée</td>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Recherche en temps réel
        const searchInput = document.getElementById('categorySearch');
        const table = document.getElementById('categoriesTable');
        const rows = table.getElementsByTagName('tr');
        
        searchInput.addEventListener('keyup', function() {
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