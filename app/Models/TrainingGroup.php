<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingGroup extends Model
{
    /** @use HasFactory<\Database\Factories\TrainingGroupFactory> */
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['name', 'start_date', 'end_date', 'instructor_id', 'branch_id', 'status'];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // trigger Like - to finish the groups the end_date has finished

    public static function boot()
    {
        parent::boot();
        // Listen for the "saved" event (fires after create or update)
        static::saved(function ($group) {
            // Call a method to update other groups
            $group->updateRelatedGroups();
        });
    }

    public function updateRelatedGroups()
    {
        // 2 months + 15 days (half a month)
        $threshold = Carbon::now()->subMonths(2)->subDays(15);

        // Update other groups (excluding the current one)
        $d = TrainingGroup::where('id', '!=', $this->id)
            ->where('end_date', '<', $threshold)   // End date is after the threshold
            ->update(['status' => 'finished']); // Set to finished (0)
    }
}
