<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    /**
     * Indique si les timestamps du modèle doivent être utilisés.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'created_at'
    ];

    /**
     * Relation avec les projets
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }
}