<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotulensiPhoto extends Model
{
    protected $fillable = [
        'notulensi_id',
        'photo_path',
    ];

    public function notulensi(): BelongsTo
    {
        return $this->belongsTo(Notulensi::class);
    }
}
