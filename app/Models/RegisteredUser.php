<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisteredUser extends Model
{
    protected $table = 'registered_users';
protected $fillable = ['user_name', 'first_name', 'last_name', 'password', 'user_type', 'created_by', 'updated_by', 'deleted_by'];

}
