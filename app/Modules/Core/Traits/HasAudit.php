<?php

namespace App\Modules\Core\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    protected static function bootHasAudit(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
