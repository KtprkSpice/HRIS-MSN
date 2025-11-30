<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class division extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'description',
        'status'
    ];
}
