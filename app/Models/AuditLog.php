<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'emp_number',
        'record_id',
        'module',
        'sub_module',
        'action',
        'previous_data',
        'new_data',
        'ip_address',
    ];
}
