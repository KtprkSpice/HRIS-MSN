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

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

}
