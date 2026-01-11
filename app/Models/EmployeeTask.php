<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTask extends Model
{
    use SoftDeletes;
    protected $table = 'employee_task';

     protected $fillable = [
        'employee_id',
        'task_id',
        'deleted_at'
    ];

    
}
