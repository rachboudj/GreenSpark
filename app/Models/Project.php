<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }
    
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
    
    public function media()
    {
        return $this->hasMany(ProjectMedia::class);
    }
}
