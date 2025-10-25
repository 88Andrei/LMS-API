<?php

namespace App\Models;

use App\Enums\FileType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
    ];

    protected $casts = [
        'type' => FileType::class,
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
