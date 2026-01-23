<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table = 'visitors';
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'visitor_type',
        'visitor_id',
        'location',
        'image_path',
        'time_out',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}
