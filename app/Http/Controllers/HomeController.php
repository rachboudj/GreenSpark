<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Récupérer quelques projets actifs ou financés pour les afficher en vedette
        $projects = Project::whereIn('status', ['active', 'funded'])
                        ->with('category')
                        ->latest()
                        ->take(3) // Limiter à 3 projets en vedette
                        ->get();
        
        return view('home', compact('projects'));
    }
}
