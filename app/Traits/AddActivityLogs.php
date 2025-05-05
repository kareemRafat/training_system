<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AddActivityLogs
{
    public static function Add($event, $value, $action, $record)
    {
        // Log the activity
        $record->activityLogs()->create([
            'action' => $action,
            'changes' => [
                'original' => '',
                'updated' => [
                    $event => $value, // Include the new comment details
                ],
            ],
            'user_id' => Auth::user()->id,
            'created_at' => now()->setTimezone(config('app.timezone'))->toDateTimeString(),
        ]);
    }
}
