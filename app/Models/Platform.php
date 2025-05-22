<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requirements' => 'json',
    ];

    /**
     * The posts that will be published to this platform.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)
            ->withPivot('platform_status', 'failure_reason')
            ->withTimestamps();
    }

    /**
     * The users who have this platform activated.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_active', 'credentials')
            ->withTimestamps();
    }

    /**
     * Get the character limit for this platform.
     */
    public function getCharacterLimit(): int
    {
        $requirements = $this->requirements ?? [];
        return $requirements['character_limit'] ?? PHP_INT_MAX;
    }
}
