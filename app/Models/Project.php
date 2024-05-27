<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model{
    use HasFactory;
        protected $fillable = ['name', 'description', 'segment_id'];

        public function segment(){
            return $this->belongsTo(Segment::class);
        }

        public function supplies(){
            return $this->hasMany(Supply::class, 'project_id');
        }

        public function catalogs(){
            return $this->hasMany(Catalog::class);
        }

        public function orders(){
            return $this->hasManyThrough(Order::class, Catalog::class);
        }
}
