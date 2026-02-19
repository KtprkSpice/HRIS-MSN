<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class leaveApproval extends Model
{
    protected $fillable = [
        'leave_request_id',
        'approval_order',
        'role_id',
        'approved_by',
        'status',
        'approved_at',
        'notes',
    ];

    public function leaveRequests()
    {
        return $this->BelongsTo(LeaveRequest::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
