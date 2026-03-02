<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VisitorType;

class Visitor extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'visitors';
    protected $fillable = [
        'full_name',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'visitor_type',
        'visitor_id',
        'location',
        'address',
        'image_path',
        'status',
        'time_in',
        'time_out',
        'purpose',
        'contact_person',
        'valid_id',
        'id_type',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
    ];

    public function visitorType()
    {
        return $this->belongsTo(VisitorType::class, 'visitor_type');
    }

    public function validIdType()
    {
        return $this->belongsTo(ValidIdType::class, 'id_type');
    }

    public function getEmpName($empCode)
    {
        // Retrieve the list you stored in session during login
        $employees = session('all_emp');

        if (!$employees) {
            return $empCode; // Return the code if session is empty
        }

        // Search the collection for the matching emp_code
        $employee = collect($employees)->firstWhere('emp_code', $empCode);

        if ($employee) {
            // Handle both object and array formats depending on your API helper
            $firstName = data_get($employee, 'first_name');
            $lastName = data_get($employee, 'last_name');
            
            return "{$firstName} {$lastName}";
        }

        return $empCode; // Return code if no match found
    }



    public function userType()
    {
        return $this->belongsTo(\App\Models\User_types::class, 'user_type', 'id');
    }
    public function getLocationNameAttribute()
    {
        $locationValue = (string) $this->location;

        if ($locationValue === '') {
            return 'N/A';
        }

        if (!is_numeric($locationValue)) {
            return $locationValue;
        }

        $locations = collect(session('all_location', []));
        $match = $locations->firstWhere('id', $locationValue);

        return $match ? $match['name'] : $locationValue;
    }
}
