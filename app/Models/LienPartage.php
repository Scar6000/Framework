<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LienPartage extends Model
{
    protected $table = 'liens_partage';

    protected $fillable = [
        'pv_id',
        'token',
        'created_by',
        'expires_at',
        'max_views',
        'views_count',
        'is_active',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'max_views' => 'integer',
            'views_count' => 'integer',
            'is_active' => 'boolean',
            'revoked_at' => 'datetime',
        ];
    }

    public function pv(): BelongsTo
    {
        return $this->belongsTo(Pv::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}