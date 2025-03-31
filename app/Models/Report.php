<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'reason',
        'description',
        'status'
    ];

    // Status constants - these should match what's stored in DB
    const STATUS_PENDING = '1';
    const STATUS_APPROVED = '2';
    const STATUS_REJECTED = '3';

    // If you're using numeric status codes in DB, use these instead:
    // const STATUS_PENDING = 1;
    // const STATUS_APPROVED = 2;
    // const STATUS_REJECTED = 3;

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for different statuses
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Helper method to get status options
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }
}