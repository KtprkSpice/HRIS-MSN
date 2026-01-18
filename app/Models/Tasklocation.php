<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tasklocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_location';

    protected $fillable = [
        'task_id',
        'name',
        'latitude',
        'longitude',
        'radius',
        'is_active',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
