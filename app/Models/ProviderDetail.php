<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'provider_id','describe']; 

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}