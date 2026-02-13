<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    protected $table = 'authorization';
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'DeleteUser',
        'EditRooms',
        'EditPricing',
        'EditImages',
        'RevokeAccess',
    ];

    protected $casts = [
        'DeleteUser' => 'boolean',
        'EditRooms' => 'boolean',
        'EditPricing' => 'boolean',
        'EditImages' => 'boolean',
        'RevokeAccess' => 'boolean',
    ];
}
