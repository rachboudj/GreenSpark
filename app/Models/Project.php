<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'goal_amount',
        'current_amount',
        'end_date',
        'status',
        'thumbnail',
        'user_id',
        'category_id',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'end_date' => 'date',
        'goal_amount' => 'float',
        'current_amount' => 'float',
    ];

    /**
     * Relation avec l'utilisateur (créateur du projet)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    /**
     * Relation avec les contributions
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Relation avec les médias
     */
    public function media()
    {
        return $this->hasMany(ProjectMedia::class);
    }

    /**
     * Vérifier si le projet est toujours actif
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    /**
     * Vérifier si l'objectif est atteint
     */
    public function isGoalReached()
    {
        return $this->current_amount >= $this->goal_amount;
    }

    /**
     * Calculer le pourcentage de financement
     */
    public function fundingPercentage()
    {
        if ($this->goal_amount <= 0) {
            return 0;
        }
        
        $percentage = ($this->current_amount / $this->goal_amount) * 100;
        return min($percentage, 100); // Limiter à 100% maximum
    }

    /**
     * Obtenir le nombre de jours restants
     */
    public function daysLeft()
    {
return floor(now()->diffInDays($this->end_date, false));
    
$days = now()->diffInDays($this->end_date, false);
return floor($days);
    }

    /**
     * Vérifier si le projet est terminé
     */
    public function isEnded()
    {
        return $this->end_date->isPast() || $this->status === 'completed' || $this->status === 'cancelled';
    }
}