<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notulensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'notulen_id',
        'kepala_id',
        'notulensi_date',
        'meeting_date',
        'location',
        'isi_notulensi',
    ];

    protected function casts(): array
    {
        return [
            'notulensi_date' => 'datetime',
            'meeting_date' => 'date',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function notulen(): BelongsTo
    {
        return $this->belongsTo(Notulen::class);
    }

    public function kepala(): BelongsTo
    {
        return $this->belongsTo(Kepala::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(NotulensiPhoto::class);
    }
}
