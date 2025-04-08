<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }
}
