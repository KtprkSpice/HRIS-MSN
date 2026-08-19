<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskShift extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_shift';

    protected $fillable = [
        'task_id',
        'name',
        'start_time',
        'end_time',
        'late_tolerance',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
