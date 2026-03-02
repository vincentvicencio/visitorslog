<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'emp_number',
        'record_id',
        'module',
        'sub_module',
        'action',
        'ip_address',
        'created_at',
        'updated_at'
    ];

     protected $casts = [
        'previous_data' => 'array',
        'new_data'      => 'array',
    ];
}
