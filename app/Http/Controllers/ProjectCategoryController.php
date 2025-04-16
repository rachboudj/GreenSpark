<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectCategory;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    /**
 * Afficher la liste des catégories (admin)
 */
public function adminIndex()
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    $categories = ProjectCategory::orderBy('name')->get();
    return view('dashboard.categories.index', compact('categories')); // Chemin mis à jour
}

/**
 * Afficher le formulaire d'ajout de catégorie (admin)
 */
public function adminCreate()
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    return view('dashboard.categories.create');
}

/**
 * Traiter la création d'une catégorie (admin)
 */
public function adminStore(Request $request)
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    // Validation des données
    $validated = $request->validate([
        'name' => 'required|string|min:2|max:50|unique:project_categories,name',
        'description' => 'nullable|string|max:255',
    ]);
    
    // Création de la catégorie
    $category = new ProjectCategory();
    $category->name = $validated['name'];
    $category->description = $validated['description'] ?? '';
    $category->created_at = now();
    $category->save();
    
    return redirect()->route('admin.categories.index')
                     ->with('success', 'Catégorie créée avec succès!');
}

/**
 * Afficher le formulaire de modification (admin)
 */
public function adminEdit($id)
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    $category = ProjectCategory::findOrFail($id);
    return view('dashboard.categories.edit', compact('category'));
}

/**
 * Traiter la mise à jour d'une catégorie (admin)
 */
public function adminUpdate(Request $request, $id)
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    $category = ProjectCategory::findOrFail($id);
    
    // Validation des données
    $validated = $request->validate([
        'name' => 'required|string|min:2|max:50|unique:project_categories,name,' . $category->id,
        'description' => 'nullable|string|max:255',
    ]);
    
    // Mise à jour de la catégorie
    $category->name = $validated['name'];
    $category->description = $validated['description'] ?? '';
    $category->save();
    
    return redirect()->route('admin.categories.index')
                     ->with('success', 'Catégorie mise à jour avec succès!');
}

/**
 * Supprimer une catégorie (admin)
 */
public function adminDestroy($id)
{
    // Vérifier si l'utilisateur est admin
    if (auth()->user()->role !== 'Admin') {
        return redirect()->route('home')->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
    
    $category = ProjectCategory::findOrFail($id);
    
    // Vérifier si des projets utilisent cette catégorie
    $projectsCount = $category->projects()->count();
    if ($projectsCount > 0) {
        return redirect()->route('admin.categories.index')
                         ->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée par ' . $projectsCount . ' projet(s).');
    }
    
    $category->delete();
    
    return redirect()->route('admin.categories.index')
                     ->with('success', 'Catégorie supprimée avec succès!');
}
}
