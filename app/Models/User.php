<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Notifications\CustomPasswordReset;

use Laravel\Scout\Searchable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Searchable;
  
      /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = ['name', 'email', 'password', 'role', 'verification_code','image',
        'first_name',
        'last_name',
        'headline',
        'about',
        'twitter_link',
        'linkedin_link',
        'youtube_link',
        'facebook_link',];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordReset($token));
    }

    // Accessors for links
    public function getTwitterLinkAttribute($value)
    {
        return $value ?? 'https://twitter.com';
    }

    public function getLinkedInLinkAttribute($value)
    {
        return $value ?? 'https://www.linkedin.com';
    }

    public function getYoutubeLinkAttribute($value)
    {
        return $value ?? 'https://www.youtube.com';
    }

    public function getFacebookLinkAttribute($value)
    {
        return $value ?? 'https://www.facebook.com';
    }    

    /*protected $guarded = [

    protected $guarded = [

        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code'
    ];

    // Optional helper methods
    public function isLearner()
    {
        return $this->role === 'learner';
    }
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoriteCourses()
    {
        return $this->belongsToMany(Course::class, 'favorites')->withTimestamps();
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class, 'user_id');
    }


    public function carts()
    {
        return $this->hasMany(Cart::class);

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'nationality' => $this->nationality,
        ];

    }
}
