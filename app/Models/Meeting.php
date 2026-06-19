<?php

namespace App\Models;

use App\Enums\MeetingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_time',
        'end_time',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => MeetingStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function meetingDays(): HasMany
    {
        return $this->hasMany(MeetingDay::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function isActive(): bool
    {
        $lastDay = $this->meetingDays()->orderByDesc('date')->first();

        return $lastDay && $lastDay->date >= now()->subDay()->toDateString();
    }

    public function getStartDateFormattedAttribute(): string
    {
        $firstDay = $this->meetingDays()->orderBy('date')->first();
        if (! $firstDay) {
            return '';
        }

        return $firstDay->date->format('Y-m-d').' '.$this->start_time;
    }

    public function getEndDateFormattedAttribute(): string
    {
        $lastDay = $this->meetingDays()->orderByDesc('date')->first();
        if (! $lastDay) {
            return '';
        }

        return $lastDay->date->format('Y-m-d').' '.$this->end_time;
    }
}
