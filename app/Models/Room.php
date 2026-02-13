<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'price',
        'image',
        'guests',
        'beds',
        'size',
        'view',
        'amenities',
        'popular',
        'occupants'
    ];

    protected $casts = [
        'amenities' => 'array',
        'popular' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Relationship with bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Append bookings to JSON response
    protected $appends = ['bookings_formatted'];

    public function getBookingsFormattedAttribute()
    {
        return $this->bookings->map(function ($booking) {
            return [
                'from' => $booking->from_date,
                'to' => $booking->to_date
            ];
        })->toArray();
    }
}