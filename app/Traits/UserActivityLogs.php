<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait UserActivityLogs
{
    public static function bootUserActivityLogs()
    {
        static::created(function ($model) {
            $model->logActivity('إنشاء');
        });

        static::updated(function ($model) {
            if ($model->isDirty('training_group_id')) {
                $model->logActivity('تغير مجموعة التدريب');

                return;
            }
            if ($model->isDirty('status')) {
                $model->logActivity('تغير الحالة');

                return;
            }
            $model->logActivity('تعديل');
        });

        static::deleted(function ($model) {
            $model->logActivity('حذف');
        });
    }

    public function logActivity($action)
    {

        $changedFields = $this->getChanges();

        $originals = collect($changedFields)->mapWithKeys(function ($newValue, $key) {
            return [$key => $this->getOriginal($key)];
        });

        if (Auth::check()) {
            $this->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => $action,
                'changes' => [
                    'original' => $originals,
                    'updated' => $this->getChanges(),
                ],
            ]);
        }
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
