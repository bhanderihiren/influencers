<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaPlatform extends Model
{
    use HasFactory;

    protected $table = 'socialmedia_platform';
    
    protected $fillable = [
        'platform',
        'user_id',
        'review_id',
        'name',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
    public function platformLink()
    {
        return $this->hasOne(SocialmediaPlatformReview::class, 'platfrom_id', 'id');     
    }
}
