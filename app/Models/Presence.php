<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'presences';

    protected $fillable = [
        'employee_id',
        'task_id',
        'date',
        'schedule_id',
        'shift_id',
        'latitude',
        'longitude',
        'distance',
        'check_in',
        'check_out',
        'status',
        'type',
    ];

    

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
