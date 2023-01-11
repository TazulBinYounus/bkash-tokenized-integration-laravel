<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable  = ['user_id', 'agreement_id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
