<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['name', 'phone', 'status', 'start', 'group_id', 'instructor_id', 'created_at', 'branch_id', 'training_group_id' ,'training_joined_at'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function training_group()
    {
        return $this->belongsTo(TrainingGroup::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
