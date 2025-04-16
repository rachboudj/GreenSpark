<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Constructeur avec middleware d'authentification
     */
    public function __construct()
    {
        // Protection des routes qui nécessitent une authentification
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Afficher la liste des projets
     */
public function index(Request $request)
{
    $query = Project::with('category', 'user');
    
    // Filtrer pour n'afficher que les projets actifs ou financés
    $query->whereIn('status', ['active', 'funded']);
    
    // Recherche par mot-clé
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('region', 'like', "%{$search}%");
        });
    }
    
    // Filtre par catégorie
    if ($request->has('category') && !empty($request->category)) {
        $query->where('category_id', $request->category);
    }
    
    // Récupération des projets
    $projects = $query->latest()->paginate(12);
    
    // Récupération des catégories pour le filtre
    $categories = ProjectCategory::all();
    
    return view('projects.index', compact('projects', 'categories'));
}

    /**
     * Afficher le formulaire de création de projet
     */
    public function create()
    {
        $categories = ProjectCategory::all();
        return view('projects.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'funding_goal' => 'required|numeric|min:1',
            'end_date' => 'required|date|after:today',
            'category_id' => 'required|exists:project_categories,id',
            'region' => 'required|string|max:255',
            'cover_image' => 'nullable|image|max:2048', // 2MB max
            'media.*' => 'nullable|image|max:2048',
        ]);
        
        // Création du projet
        $project = new Project();
        $project->title = $validated['title'];
        $project->slug = Str::slug($validated['title']) . '-' . Str::random(5); // Génération d'un slug unique
        $project->short_description = $validated['short_description'];
        $project->description = $validated['description'];
        $project->funding_goal = $validated['funding_goal'];
        $project->current_amount = 0; // Montant initial à 0
        $project->start_date = now(); // Date actuelle comme date de début
        $project->end_date = $validated['end_date'];
        $project->status = 'active';
        $project->region = $validated['region'];
        $project->category_id = $validated['category_id'];
        $project->user_id = auth()->id();
        
        // Traitement de l'image principale
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('project_covers', 'public');
            $project->cover_image = $coverImagePath;
        }
        
        $project->save();
        
        // Traitement des médias supplémentaires (images)
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('project_media', 'public');
                
                // Création d'un enregistrement dans project_media
                $media = new ProjectMedia();
                $media->project_id = $project->id;
                $media->file_path = $path;
                $media->file_type = $file->getClientMimeType();
                $media->save();
            }
        }
        
        return redirect()->route('projects.show', $project->slug)
                         ->with('success', 'Votre projet a été créé avec succès !');
    }

    /**
     * Afficher un projet spécifique
     */
    public function show(string $slug)
    {
        $project = Project::where('slug', $slug)
                          ->with(['user', 'category', 'media', 'contributions.user'])
                          ->firstOrFail();
        
        // Calcul du pourcentage d'avancement
        $progressPercentage = ($project->current_amount / $project->funding_goal) * 100;
        $progressPercentage = min($progressPercentage, 100); // Limiter à 100% maximum
        
        // Jours restants
        $daysLeft = now()->diffInDays($project->end_date, false);
        
        return view('projects.show', compact('project', 'progressPercentage', 'daysLeft'));
    }

    /**
     * Afficher le formulaire d'édition d'un projet
     */
    public function edit(string $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        // Vérifier que l'utilisateur est bien le propriétaire du projet
        if ($project->user_id !== auth()->id()) {
            return redirect()->route('projects.show', $project->slug)
                             ->with('error', 'Vous n\'êtes pas autorisé à modifier ce projet.');
        }
        
        $categories = ProjectCategory::all();
        
        return view('projects.edit', compact('project', 'categories'));
    }

    /**
     * Mettre à jour un projet existant
     */
    public function update(Request $request, string $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        // Vérifier que l'utilisateur est bien le propriétaire du projet
        if ($project->user_id !== auth()->id()) {
            return redirect()->route('projects.show', $project->slug)
                             ->with('error', 'Vous n\'êtes pas autorisé à modifier ce projet.');
        }
        
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string',
            'funding_goal' => 'required|numeric|min:1',
            'end_date' => 'required|date|after:today',
            'region' => 'required|string|max:255',
            'category_id' => 'required|exists:project_categories,id',
            'cover_image' => 'nullable|image|max:2048',
            'media.*' => 'nullable|image|max:2048',
        ]);
        
        // Mise à jour du projet
        $project->title = $validated['title'];
        // Ne pas changer le slug pour ne pas casser les liens
        $project->short_description = $validated['short_description'];
        $project->description = $validated['description'];
        $project->funding_goal = $validated['funding_goal'];
        $project->end_date = $validated['end_date'];
        $project->region = $validated['region'];
        $project->category_id = $validated['category_id'];
        
        // Traitement de l'image principale
        if ($request->hasFile('cover_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }
            
            $coverImagePath = $request->file('cover_image')->store('project_covers', 'public');
            $project->cover_image = $coverImagePath;
        }
        
        $project->save();
        
        // Traitement des médias supplémentaires
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('project_media', 'public');
                
                $media = new ProjectMedia();
                $media->project_id = $project->id;
                $media->file_path = $path;
                $media->file_type = $file->getClientMimeType();
                $media->save();
            }
        }
        
        return redirect()->route('projects.show', $project->slug)
                         ->with('success', 'Votre projet a été mis à jour avec succès !');
    }

    /**
     * Supprimer un projet spécifique
     */
    public function destroy(string $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        // Vérifier que l'utilisateur est bien le propriétaire du projet
        if ($project->user_id !== auth()->id()) {
            return redirect()->route('projects.show', $project->slug)
                             ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce projet.');
        }
        
        // Vérifier si le projet peut être supprimé (pas de contributions)
        if ($project->contributions()->count() > 0) {
            return redirect()->route('projects.show', $project->slug)
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
        
        return redirect()->route('projects.index')
                         ->with('success', 'Le projet a été supprimé avec succès.');
    }
    
    /**
     * Afficher les projets par catégorie
     */
    public function byCategory(int $id)
    {
        $category = ProjectCategory::findOrFail($id);
        $projects = Project::where('category_id', $category->id)
                          ->with('user')
                          ->latest()
                          ->paginate(12);
        
        return view('projects.category', compact('projects', 'category'));
    }
    
    /**
     * Supprimer un média associé à un projet
     */
    public function deleteMedia(int $mediaId)
    {
        $media = ProjectMedia::findOrFail($mediaId);
        $project = $media->project;
        
        // Vérifier que l'utilisateur est bien le propriétaire du projet
        if ($project->user_id !== auth()->id()) {
            return redirect()->route('projects.edit', $project->slug)
                             ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce média.');
        }
        
        // Supprimer le fichier
        Storage::disk('public')->delete($media->file_path);
        
        // Supprimer l'enregistrement
        $media->delete();
        
        return redirect()->route('projects.edit', $project->slug)
                         ->with('success', 'Le média a été supprimé avec succès.');
    }
}