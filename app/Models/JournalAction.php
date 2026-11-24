<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalAction extends Model
{
    protected $fillable = [
        'user_id',
        'pv_deliberation_id',
        'action',
        'details',
        'adresse_ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pvDeliberation(): BelongsTo
    {
        return $this->belongsTo(PvDeliberation::class);
    }
}