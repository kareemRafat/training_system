<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingGroup extends Model
{
    /** @use HasFactory<\Database\Factories\TrainingGroupFactory> */
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['name', 'start_date', 'end_date', 'instructor_id', 'branch_id'];

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
}
