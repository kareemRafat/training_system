<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait UserActivityLogs
{
    public static function bootUserActivityLogs()
    {
        static::created(function ($model) {
            $model->logActivity('إنشاء');
        });

        static::updated(function ($model) {
            $model->logActivity('تعديل');
        });

        static::deleted(function ($model) {
            $model->logActivity('حذف');
        });
    }

    public function logActivity($action)
    {
        if (Auth::check()) {
            $this->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => $action,
                'changes' => json_encode($this->getChanges()),
            ]);

        }
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
