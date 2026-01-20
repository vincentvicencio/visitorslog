<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'emp_code',
        'tr_no',
        'password',
        'first_name',
        'last_name',
        'email',
        'company_id',
        'location_id',
        'department_id',
        'division_id',
        'section_id',
        'job_title_id',
        'cluster_id',
        'is_approver',
        'contact_no',
    ];

    public function getAuthIdentifierName()
    {
        return $this->identifierKey ?? 'emp_code';
    }

    public function setIdentifierKey($key)
    {
        $this->identifierKey = $key;
    }
}
