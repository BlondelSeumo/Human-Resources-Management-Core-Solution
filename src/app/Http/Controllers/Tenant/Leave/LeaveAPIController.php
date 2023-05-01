<?php


namespace App\Http\Controllers\Tenant\Leave;


use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\LeaveType;

class LeaveAPIController extends Controller
{
    public function index()
    {
        return LeaveType::where('is_enabled', 1)->get(['id', 'name']);
    }
}