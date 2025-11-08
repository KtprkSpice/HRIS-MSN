<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'leave_type',
        'status',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
