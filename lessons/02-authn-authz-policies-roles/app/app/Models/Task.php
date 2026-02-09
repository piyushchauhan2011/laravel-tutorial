<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'details',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
