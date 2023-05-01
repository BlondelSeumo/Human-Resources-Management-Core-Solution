<?php


namespace App\Http\Controllers\Tenant\WorkingShift;


use App\Http\Controllers\Controller;
use App\Models\Tenant\WorkingShift\WorkingShift;

class WorkingShiftAPIController extends Controller
{
    public function index()
    {
        return WorkingShift::query()->select(['id','name'])->get();
    }
}