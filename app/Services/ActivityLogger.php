<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    /** Mettre à false pour couper le journal (seeders). */
    public static bool $actif = true;

    public static function log(
        string $action,
        ?Model $subject = null,
        ?array $anciennes = null,
        ?array $nouvelles = null,
        ?int $userId = null
    ): ?ActivityLog {
        if (! self::$actif) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'entite' => $subject?->getMorphClass() ?? 'systeme',
            'entite_id' => $subject?->getKey(),
            'description' => json_encode([
                'anciennes_valeurs' => $anciennes,
                'nouvelles_valeurs' => $nouvelles,
            ], JSON_UNESCAPED_UNICODE),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 250, ''),
            'created_at' => now(),
        ]);
    }
}
