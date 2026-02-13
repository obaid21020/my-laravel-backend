<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Check if token is expired (24 hours)
     */
    public function isExpired(): bool
    {
        return $this->created_at->addHours(24)->isPast();
    }

    /**
     * Check if user has recent reset request (within 24 hours)
     */
    public static function hasRecentRequest(string $email): bool
    {
        return self::where('email', $email)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->exists();
    }
}
