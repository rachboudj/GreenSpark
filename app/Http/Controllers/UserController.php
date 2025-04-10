<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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


    public function dashboard()
{
    $user = auth()->user();
    
    // Récupérez les projets de l'utilisateur
    $projects = $user->projects()->latest()->get();
    
    // Récupérez les contributions de l'utilisateur
    $contributions = $user->contributions()->with('project')->latest()->get();
    
    return view('dashboard.index', compact('user', 'projects', 'contributions'));
}

// Ajoutez cette méthode dans votre UserController.php

/**
 * Afficher la liste des projets de l'utilisateur connecté
 */
public function myProjects()
{
    $user = auth()->user();
    
    // Récupérez les projets de l'utilisateur, triés par date de création décroissante
    $projects = $user->projects()->with('category')->latest()->get();
    
    return view('dashboard.my-projects', compact('user', 'projects'));
}

/**
 * Afficher la liste des contributions de l'utilisateur connecté
 */
public function myContributions()
{
    $user = auth()->user();
    
    // Récupérez les contributions de l'utilisateur, avec les projets associés
    $contributions = $user->contributions()->with('project.user', 'project.category')->latest()->get();
    
    return view('dashboard.my-contributions', compact('user', 'contributions'));
}
}
