<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContributionController extends Controller
{
    /**
     * Constructeur avec middleware d'authentification
     */
    public function __construct()
    {
        // Protection des routes qui nécessitent une authentification
        $this->middleware('auth');
    }

    /**
     * Afficher le formulaire de contribution
     */
    public function create(string $slug)
    {
        $project = Project::where('slug', $slug)
                          ->with(['user', 'category'])
                          ->firstOrFail();
        
        // Vérifier si le projet est toujours actif
        if ($project->status !== 'active' || now()->gt($project->end_date)) {
            return redirect()->route('projects.show', $project->slug)
                             ->with('error', 'Ce projet n\'accepte plus de contributions.');
        }
        
        return view('contributions.create', compact('project'));
    }

    /**
     * Traiter la contribution
     */
    public function store(Request $request, string $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        // Vérifier si le projet est toujours actif
        if ($project->status !== 'active' || now()->gt($project->end_date)) {
            return redirect()->route('projects.show', $project->slug)
                             ->with('error', 'Ce projet n\'accepte plus de contributions.');
        }
        
        // Validation des données
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:credit_card,paypal',
            'terms' => 'required|accepted',
        ]);
        
        // Création de la contribution
        $contribution = new Contribution();
        $contribution->user_id = auth()->id();
        $contribution->project_id = $project->id;
        $contribution->amount = $validated['amount'];
        $contribution->payment_status = 'completed'; // Simule un paiement réussi
        $contribution->transaction_id = 'tr_' . strtolower(Str::random(16)); // Génère un ID de transaction factice
        $contribution->save();
        
        // Mise à jour du montant collecté pour le projet
        $project->current_amount += $validated['amount'];
        $project->save();
        
        return redirect()->route('projects.show', $project->slug)
                         ->with('success', 'Merci pour votre contribution de ' . number_format($validated['amount'], 2, ',', ' ') . ' € !');
    }
    
    /**
     * Afficher les contributions d'un utilisateur
     */
    public function userContributions()
    {
        $contributions = Contribution::where('user_id', auth()->id())
                                     ->with('project')
                                     ->latest()
                                     ->paginate(10);
        
        return view('contributions.user-contributions', compact('contributions'));
    }
}