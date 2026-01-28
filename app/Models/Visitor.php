<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

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
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
    ];

    public function visitor_type()
    {
        return $this->belongsTo(\App\Models\VisitorType::class, 'visitor_type', 'id');
    }
    public function userType()
    {
        return $this->belongsTo(\App\Models\User_types::class, 'user_type', 'id');
    }
    public function getLocationNameAttribute()
    {
        $locations = collect(session('all_location'));
        $match = $locations->firstWhere('id', $this->location);
        return $match ? $match['name'] : 'N/A';
    }

}
