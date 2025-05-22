<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Get all posts for the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    
    /**
     * Get all platforms that the user has activated.
     */
    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class)
            ->withPivot('is_active', 'credentials')
            ->withTimestamps();
    }
    
    /**
     * Get all activity logs for the user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
    
    /**
     * Check if the user has exceeded their daily post limit.
     */
    public function hasReachedDailyPostLimit(): bool
    {
        $todayPosts = $this->posts()
            ->whereDate('created_at', now()->toDateString())
            ->where('status', '!=', 'draft')
            ->count();
        
        return $todayPosts >= 10; // Maximum 10 posts per day
    }
}
