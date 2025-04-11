<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ProjectCategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification (générées par Auth::routes())
Auth::routes();

// Routes pour les projets
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{slug}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{slug}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/projects/{slug}', [ProjectController::class, 'destroy'])->name('projects.destroy');
// Route::delete('/project-media/{id}', [ProjectController::class, 'deleteMedia'])->name('project-media.destroy');
Route::delete('/project-media/{id}', [App\Http\Controllers\ProjectMediaController::class, 'destroy'])->name('projects.media.delete');

// Routes pour les catégories
Route::get('/category/{id}', [ProjectController::class, 'byCategory'])->name('projects.category');
Route::post('/categories', [ProjectCategoryController::class, 'store'])->name('categories.store');

// Routes pour le tableau de bord admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::delete('/projects/{id}', [App\Http\Controllers\AdminController::class, 'deleteProject'])->name('delete-project');
    Route::patch('/projects/{id}/status', [App\Http\Controllers\AdminController::class, 'updateProjectStatus'])->name('update-project-status');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::patch('/users/{id}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('update-user-role');
});

// Routes pour le tableau de bord utilisateur
Route::get('/my-projects', [UserController::class, 'myProjects'])->name('my-projects');
Route::get('/my-contributions', [UserController::class, 'myContributions'])->name('my-contributions');

// Routes pour l'administration des catégories (avec middleware admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin/categories', ProjectCategoryController::class);
});

// Routes pour les contributions
Route::middleware(['auth'])->group(function () {
    Route::get('/projects/{slug}/contribute', [ContributionController::class, 'create'])->name('contributions.create');
    Route::post('/projects/{slug}/contribute', [ContributionController::class, 'store'])->name('contributions.store');
});