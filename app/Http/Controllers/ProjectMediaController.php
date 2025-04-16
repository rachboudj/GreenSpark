<?php

namespace App\Http\Controllers;

use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectMediaController extends Controller
{
    /**
     * Constructeur avec middleware d'authentification
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Supprimer un média spécifique
     */
    public function destroy(string $id)
    {
        $media = ProjectMedia::findOrFail($id);
        $project = $media->project;
        
        // Vérifier que l'utilisateur est bien le propriétaire du projet
        if ($project->user_id !== auth()->id()) {
            return redirect()->back()
                             ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce média.');
        }
        
        // Supprimer le fichier
        Storage::disk('public')->delete($media->file_path);
        
        // Supprimer l'enregistrement
        $media->delete();
        
        return redirect()->back()
                         ->with('success', 'Le média a été supprimé avec succès.');
    }
}