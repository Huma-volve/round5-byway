<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_earnings',
        'bio',
        'twitter_link',
        'linkdin_link',
        'youtube_link',
        'facebook_link',
        'name',
        'image',
        'role'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}