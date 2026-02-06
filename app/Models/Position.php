<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'division_id',
        'base_salary',
        'cut_per_minute',
    ];

    public function division()
    {
        return $this->belongsTo(division::class);
    }
}
