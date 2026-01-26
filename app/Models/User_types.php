<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_types extends Model
{
    protected $table = 'user_types';

    // Add updated_by to this list
    protected $fillable = ['name', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at']; 
}