<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_types extends Model
{
    protected $table = 'user_types';
    protected $primaryKey = 'id';
public $incrementing = true;

    // Add updated_by to this list
    protected $fillable = ['id',
    'name',
    'created_by',
    'updated_by',
    'deleted_by',
    'created_at',
    'updated_at',
    'deleted_at'
    
    ]; 
}