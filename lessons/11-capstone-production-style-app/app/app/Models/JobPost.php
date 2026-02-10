<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'status',
        'is_remote',
        'salary_min',
        'salary_max',
        'description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_remote' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
