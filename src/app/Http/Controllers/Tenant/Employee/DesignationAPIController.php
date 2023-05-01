<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Designation;

class DesignationAPIController extends Controller
{
    public function index()
    {
        return Designation::select('id','name')->get();
    }
}