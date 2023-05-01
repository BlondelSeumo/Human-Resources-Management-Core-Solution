<?php


namespace App\Models\Tenant\Employee;


use App\Models\Core\Auth\Profile as BaseProfile;
use App\Models\Tenant\Traits\ProfileRelationship;

class Profile extends BaseProfile
{
    use ProfileRelationship;

    protected $fillable = [
        'gender', 'date_of_birth', 'address', 'contact', 'joining_date', 'employee_id', 'user_id', 'phone_number',
        'marital_status', 'fathers_name', 'mothers_name', 'social_security_number', 'department_id', 'designation_id', 'about_me'
    ];

    protected $dates = [
        'date_of_birth', 'joining_date'
    ];
}
