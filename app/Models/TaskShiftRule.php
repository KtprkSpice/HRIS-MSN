<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskShiftRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_shift_rules';

    protected $fillable = [
        'task_id',
        'shift_id',
        'min_employee',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
