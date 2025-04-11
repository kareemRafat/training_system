<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['name', 'branch_id', 'active'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
