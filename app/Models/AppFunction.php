<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFunction extends Model
{
    use HasFactory;
    protected $table = 'functions';
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
