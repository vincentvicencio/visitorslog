<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLogs extends Model
{
    //
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'emp_logs';
    protected $fillable = [
        'emp_code',
        'full_name',
        'first_name',
        'last_name',
        'profile_pic',
        'location',
        'status',
        'time_in',
        'time_out',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
    ];
}
