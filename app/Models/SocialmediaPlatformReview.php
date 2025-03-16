<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaPlatformReview extends Model
{
    use HasFactory;
    protected $table = 'socialmedia_platform_reviews';
    protected $fillable = ['platfrom_id', 'platform_link'];
}



?>