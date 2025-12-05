<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'device_id',
        'name',
        'age',
        'longitude',
        'latitude',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get the pictures for the user.
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(UserPicture::class);
    }

    /**
     * Get the likes made by this user.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    /**
     * Get the dislikes made by this user.
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(Dislike::class, 'user_id');
    }

    /**
     * Get the likes received by this user (people who liked this user).
     */
    public function receivedLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'target_user_id');
    }
}
