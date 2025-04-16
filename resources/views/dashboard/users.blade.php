@extends('layouts.app')

@section('title', 'Gestion des utilisateurs | Green Spark')

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
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action active">
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
                <h1>Gestion des utilisateurs</h1>
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
                        <span>Tous les utilisateurs</span>
                        <input type="text" class="form-control form-control-sm w-25" id="userSearch" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Projets</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <form action="{{ route('admin.update-user-role', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="Inscrit" {{ $user->role == 'Inscrit' ? 'selected' : '' }}>Inscrit</option>
                                                    <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $user->projects_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-sm btn-outline-primary" title="Voir les projets" onclick="alert('Fonctionnalité à venir')">
                                                    <i class="bi bi-folder"></i> Voir les projets
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-info" title="Voir les contributions" onclick="alert('Fonctionnalité à venir')">
                                                    <i class="bi bi-cash-coin"></i> Voir les contributions
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun utilisateur trouvé</td>
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
        const searchInput = document.getElementById('userSearch');
        const table = document.getElementById('usersTable');
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