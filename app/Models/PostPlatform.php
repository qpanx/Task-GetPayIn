<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostPlatform extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_id',
        'platform_id',
        'platform_status',
        'failure_reason',
    ];
} 