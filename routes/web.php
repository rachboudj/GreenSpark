<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ProjectCategoryController;


// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification
Auth::routes(['verify' => true]);

// CRUD Projets
Route::resource('projects', ProjectController::class);

// CRUD Catégories (admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('categories', ProjectCategoryController::class);
});

// Contributions
Route::post('projects/{project}/contribute', [ContributionController::class, 'store'])->name('contributions.store');
Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index')->middleware('auth');

// Dashboard utilisateur
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('my-projects', [ProjectController::class, 'myProjects'])->name('my-projects');
    Route::get('my-contributions', [ContributionController::class, 'myContributions'])->name('my-contributions');
});