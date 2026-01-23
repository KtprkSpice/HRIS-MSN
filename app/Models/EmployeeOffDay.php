<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeOffDay extends Model
{
    protected $table = 'employee_off_days';

    protected $fillable = [
        'employee_id',
        'task_id',
        'day_of_week',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
