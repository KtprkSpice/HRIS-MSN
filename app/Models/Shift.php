<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'cross_day',
        'late_tolerance_minutes',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function weeklyShiftAssginments()
    {
        return $this->hasMany(WeeklyShiftAssignment::class);
    }

    public function TaskShiftRules()
    {
        return $this->hasMany(TaskShiftRule::class);
    }
}
