<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;
use Illuminate\Support\Arr;

/**
 * Journalise automatiquement création / modification / suppression.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            ActivityLogger::log('creation', $model, null, Arr::except($model->getAttributes(), $model->auditExclus()));
        });

        static::updated(function ($model) {
            $nouvelles = Arr::except($model->getChanges(), $model->auditExclus());
            if (empty($nouvelles)) {
                return;
            }
            $anciennes = array_intersect_key($model->getOriginal(), $nouvelles);
            ActivityLogger::log('modification', $model, $anciennes, $nouvelles);
        });

        static::deleted(function ($model) {
            ActivityLogger::log('suppression', $model, Arr::except($model->getOriginal(), $model->auditExclus()));
        });
    }

    protected function auditExclus(): array
    {
        return ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];
    }
}
