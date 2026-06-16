<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'name',
        'jenis_peserta',
        'tipe_peserta',
        'nip',
        'nik',
        'signature_data',
        'declaration',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'declaration' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
