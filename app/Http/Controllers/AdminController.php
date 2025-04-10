<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Constructeur avec middleware d'authentification admin
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le tableau de bord principal
     */
    public function index()
    {
        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        // Récupérer tous les projets avec les relations
        $projects = Project::with(['user', 'category', 'contributions'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        // Statistiques générales
        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'active')->count(),
            'total_users' => User::count(),
            'total_contributions' => Contribution::sum('amount'),
        ];
        
        return view('dashboard.index', compact('projects', 'stats'));
    }

    /**
     * Supprimer un projet
     */
    public function deleteProject($id)
    {
        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $project = Project::findOrFail($id);
        
        // Vérifier si le projet a des contributions
        if ($project->contributions()->count() > 0) {
            return redirect()->route('admin.dashboard')
                             ->with('error', 'Ce projet ne peut pas être supprimé car il a déjà reçu des contributions.');
        }
        
        // Supprimer les fichiers associés
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }
        
        // Supprimer les médias associés
        foreach ($project->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }
        
        // Supprimer le projet
        $project->delete();
        
        return redirect()->route('admin.dashboard')
                         ->with('success', 'Le projet a été supprimé avec succès.');
    }

    /**
     * Modifier le statut d'un projet
     */
    public function updateProjectStatus(Request $request, $id)
    {
        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:draft,pending,active,funded,closed',
        ]);
        
        $project = Project::findOrFail($id);
        $project->status = $validated['status'];
        $project->save();
        
        return redirect()->route('admin.dashboard')
                         ->with('success', 'Le statut du projet a été mis à jour avec succès.');
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function users()
    {
        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $users = User::withCount('projects')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('dashboard.users', compact('users'));
    }

    /**
     * Changer le rôle d'un utilisateur
     */
    public function updateUserRole(Request $request, $id)
    {
        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role !== 'Admin') {
            return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $validated = $request->validate([
            'role' => 'required|in:Admin,Inscrit',
        ]);
        
        $user = User::findOrFail($id);
        $user->role = $validated['role'];
        $user->save();
        
        return redirect()->route('admin.users')
                         ->with('success', 'Le rôle de l\'utilisateur a été mis à jour avec succès.');
    }
}