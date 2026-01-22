<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisteredID extends Model
{
    protected $table = 'registered_visitor_ids';

    protected $fillable = [
    'visitor_type',
    'id_number',
    'created_by',
    'updated_by',
    'deleted_by',
    'deleted_at'
];

}
