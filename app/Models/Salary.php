<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'salary';
    protected $fillable = [
        'employee_id',
        'net_salary',
        'cuts',
        'bonus',
        'date',
        'total'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
