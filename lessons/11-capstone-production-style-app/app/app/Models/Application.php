<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    public const STAGE_APPLIED = 'applied';
    public const STAGE_SCREENING = 'screening';
    public const STAGE_INTERVIEW = 'interview';
    public const STAGE_OFFER = 'offer';
    public const STAGE_HIRED = 'hired';
    public const STAGE_REJECTED = 'rejected';
    public const STAGE_STALE = 'stale';

    protected $fillable = [
        'job_post_id',
        'referred_by_application_id',
        'candidate_name',
        'email',
        'source',
        'stage',
        'years_experience',
        'fit_score',
        'cover_letter',
        'resume_text',
        'applied_at',
        'reviewed_at',
        'hired_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'hired_at' => 'datetime',
        ];
    }

    /**
     * @return list<string>
     */
    public static function stages(): array
    {
        return [
            self::STAGE_APPLIED,
            self::STAGE_SCREENING,
            self::STAGE_INTERVIEW,
            self::STAGE_OFFER,
            self::STAGE_HIRED,
            self::STAGE_REJECTED,
            self::STAGE_STALE,
        ];
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by_application_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by_application_id');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(ApplicationStatusEvent::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
