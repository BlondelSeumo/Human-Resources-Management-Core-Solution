<?php

use App\Http\Controllers\Tenant\Export\AttendanceExportController;
use App\Http\Controllers\Tenant\Export\LeaveExportController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => ['check_behavior', 'can_access:view_all_attendance'] ], function (Router $router) {

    $router->get('export/{employee}/attendance',[AttendanceExportController::class,'exportEmployeeAttendance'])->name('attendance-summery.export');

    $router->get('export/attendance/daily-log',[AttendanceExportController::class,'exportDailyLogAttendance'])->name('attendance-daily-log.export');

//    $router->get('export/attendance/all',[AttendanceExportController::class,'exportAllEmployeeAttendance'])->name('all-attendance-summery.export');

    $router->get('export/{employee}/leave',[LeaveExportController::class,'exportEmployeeLeave'])->name('leave-summery.export');
});