<?php

use App\Http\Controllers\Tenant\Attendance\AttendanceCommentController;
use App\Http\Controllers\Tenant\Attendance\AttendanceDailyLogController;
use App\Http\Controllers\Tenant\Attendance\AttendanceDetailsController;
use App\Http\Controllers\Tenant\Attendance\AttendanceSettingController;
use App\Http\Controllers\Tenant\Attendance\AttendanceRequestController;
use App\Http\Controllers\Tenant\Attendance\AttendanceUpdateController;
use App\Http\Controllers\Tenant\Attendance\AttendanceStatusController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => ['can_access:view_all_attendance', 'check_behavior']], function (Router $route) {
    $route->get('settings/attendances', [AttendanceSettingController::class, 'index'])
        ->name('attendance-settings.index');

    $route->post('settings/attendances', [AttendanceSettingController::class, 'update'])
        ->name('attendance-settings.update');

    $route->group(['prefix' => 'attendances'], function (Router $router) {
        $router->get('details', [AttendanceDetailsController::class, 'index'])
            ->name('attendances-details.index');

        $router->get('details/{attendance_details}', [AttendanceUpdateController::class, 'index'])
            ->name('attendance-request.send');

        $router->get('daily-log', [AttendanceDailyLogController::class, 'index'])
            ->name('daily-log.attendances');

        $router->get('request', [AttendanceRequestController::class, 'index'])
            ->name('attendance-requests.index');

        $router->patch('comments/{comment}', [AttendanceCommentController::class, 'update'])
            ->name('attendance-notes.update');

        $router->post('{details}/status/approve', [AttendanceStatusController::class, 'update'])
            ->name('attendance.approve');

        $router->post('{details}/status/reject', [AttendanceStatusController::class, 'update'])
            ->name('attendance.reject');

        $router->post('{details}/status/cancel', [AttendanceStatusController::class, 'update'])
            ->name('attendance.cancel');

        $router->patch('{attendance_details}/request', [AttendanceUpdateController::class, 'request'])
            ->name('attendance-request.send');
    });
});