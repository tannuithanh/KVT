<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}