<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VisitorType;

class RegisteredID extends Model
{
    use SoftDeletes;
    protected $table = 'registered_visitor_ids';

    protected $fillable = [
        'visitor_type',
        'id_number',
        'location',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

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

    public function visitorType()
    {
        return $this->belongsTo(VisitorType::class, 'visitor_type');
    }

   // RegisteredID model
    public function visitorsLogs()
    {
        return $this->hasMany(
            Visitor::class,
            'visitor_id',
            'id_number'
        )->whereNull('time_out');
    }



}
