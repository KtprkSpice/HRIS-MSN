<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    protected $fillable = [
        'employee_id',
        'task_id',
        'shift_id',
        'date',
        'source',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shifts::class);
    }
}
