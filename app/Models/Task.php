<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'status',
    ];

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employees_tasks')
            ->withTimestamps()
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function location()
    {
        return $this->hasMany(Tasklocation::class);
    }

    public function weeklyShiftAssginments()
    {
        return $this->hasMany(WeeklyShiftAssignment::class);
    }

    public function EmployeeOffDays()
    {
        return $this->hasMany(EmployeeOffDay::class);
    }

    public function TaskShiftRules()
    {
        return $this->hasMany(TaskShiftRule::class);
    }

    public function employeesWithTrashed()
    {
        return $this->belongsToMany(Employee::class, 'employees_tasks')
            ->withTimestamps()
            ->withPivot('deleted_at');
    }

    public function shiftRules()
    {
        return $this->hasMany(TaskShiftRule::class);
    }
}
