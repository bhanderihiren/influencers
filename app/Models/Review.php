<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    
    protected $fillable = [
        'customer_id',
        'user_id',
        'performance',
        'lead',
        'overall_review',
        'review',
    ];

    public $timestamps = true;

    protected $casts = [
        'performance' => 'float',
        'lead' => 'float',
        'overall_review' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function platform()
    {
        return $this->hasMany(SocialMediaPlatform::class, 'review_id', 'id');
    }
}