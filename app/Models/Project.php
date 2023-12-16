<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'segment_id']; // Cập nhật fillable để bao gồm 'segment_id' thay vì 'brand_id'

    public function segment()
    {
        return $this->belongsTo(Segment::class); // Cập nhật mối quan hệ để liên kết với Segment thay vì Brand
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class, 'project_id');
    }
}