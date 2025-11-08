<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['fullname', 'nik', 'division_id', 'address', 'email', 'user_id', 'phone', 'hire_date', 'born_date', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'npwp', 'status'];

    protected static function booted()
    {
        static::deleting(function ($employee) {
            if ($employee->isForceDeleting()) {
                throw new \Exception('Force delete tidak diizinkan. Gunakan soft delete saja.');
            }
            $employee->leaveRequest()->delete();
            $employee->user()->delete();
        });

        static::restoring(function ($employee) {
            $employee->leaveRequest()->restore();
            $employee->user()->restore();
        });
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }
}
