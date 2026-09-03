<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStore extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'url', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
