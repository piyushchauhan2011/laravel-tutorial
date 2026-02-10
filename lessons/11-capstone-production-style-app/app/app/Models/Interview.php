<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'interviewer',
        'starts_at',
        'ends_at',
        'mode',
        'status',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
