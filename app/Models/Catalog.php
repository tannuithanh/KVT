<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'name', 'description'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}
