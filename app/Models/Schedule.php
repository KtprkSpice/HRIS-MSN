<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schedules';

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
        return $this->belongsTo(Shift::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }
}
