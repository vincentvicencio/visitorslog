<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Add this for deleted_at

class ValidIdType extends Model
{
    use SoftDeletes; // Matches the $table->softDeletes() in your migration

    // Tell Laravel the exact table name
    protected $table = 'idtypes';

    // Allow these fields to be filled during create/update
    protected $fillable = [
        'id_type_name',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}