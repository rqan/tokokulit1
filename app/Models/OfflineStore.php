<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineStore extends Model
{
    protected $fillable = ['name', 'description', 'address', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
