<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'from_date',
        'to_date'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}