<?php

namespace App\Models;

use App\Traits\UserActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepeatedStudent extends Model
{
    /** @use HasFactory<\Database\Factories\RepeatedSutdentFactory> */
    use HasFactory , UserActivityLogs;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'track_start',
        'repeat_status',
        'group_id',
        'instructor_id',
        'branch_id',
        'created_at',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
