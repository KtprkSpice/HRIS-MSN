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
        return $this->belongsToMany(Employee::class, 'employees_tasks', 'task_id', 'employee_id');
    }
}
