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

}
