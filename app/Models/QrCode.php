<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'task_id',
        'date',
        'generated_at',
        'expires_at',
        'is_active',
        'type',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
