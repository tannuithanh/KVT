<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function supplies()
    {
        return $this->hasMany(Supply::class, 'project_id');
    }
}
