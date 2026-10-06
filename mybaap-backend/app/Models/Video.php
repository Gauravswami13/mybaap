<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'thumbnail',
        'playback_path',
        'duration_seconds',
        'is_paid',
        'is_active',
        'sort_order',
    ];

    protected $hidden = [
        'playback_path',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
