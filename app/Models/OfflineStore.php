<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineStore extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'map_link',
        'is_active',
        'order'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
