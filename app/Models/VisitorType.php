<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorType extends Model
{
    protected $table = 'visitor_types';

    protected $fillable = [
    'name',
    'created_by',
    'updated_by',
    'deleted_by',
    'deleted_at'
];

}
