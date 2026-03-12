<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $fillable = [
        'allowance_type',
        'calculation_type',
        'amount',
        'percentage_value',
        'percentage_value',
    ];
}
