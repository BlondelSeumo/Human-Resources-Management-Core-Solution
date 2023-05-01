<?php

use App\Http\Controllers\Tenant\Employee\EmployeeLeaveAllowanceController;
use App\Http\Controllers\Tenant\Leave\LeaveAssignController;
use App\Http\Controllers\Tenant\Leave\LeaveCalendarController;
use App\Http\Controllers\Tenant\Leave\LeaveCommentController;
use App\Http\Controllers\Tenant\Leave\LeavePeriodController;
use App\Http\Controllers\Tenant\Leave\LeaveRequestController;
use App\Http\Controllers\Tenant\Leave\LeaveStatusController;
use App\Http\Controllers\Tenant\Leave\LeaveSettingController;
use App\Http\Controllers\Tenant\Leave\LeaveStatusSummaryController;
use App\Http\Controllers\Tenant\Leave\LeaveSummeryController;
use App\Http\Controllers\Tenant\Leave\LeaveTypeController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => ['check_behavior', 'can_access:view_all_leaves']], function (Router $router) {
    $router->apiResource('leave-types', LeaveTypeController::class);

    $router->apiResource('leave-periods', LeavePeriodController::class);

    $router->get('leaves/request', [LeaveRequestController::class, 'index'])
        ->name('leave-requests.index');

    $router->patch('leaves/request/comments/{comment}', [LeaveCommentController::class, 'update'])
        ->name('leave-notes.update');

    $router->post('leaves/request/{leave}/rejected', [LeaveStatusController::class, 'update'])
        ->name('reject-leave.manage');

    $router->post('leaves/request/{leave}/approved', [LeaveStatusController::class, 'update'])
        ->name('approve-leave.manage');

    $router->post('leaves/request/{leave}/canceled', [LeaveStatusController::class, 'update'])
        ->name('cancel-leave.manage');

    $router->post('leaves/request/{leave}/bypassed', [LeaveStatusController::class, 'update'])
        ->name('bypass-leave.manage');

    $router->get('leaves/summaries', [LeaveStatusSummaryController::class, 'summaries'])
        ->name('leave-status.index');

    $router->get('leaves/data-table', [LeaveStatusSummaryController::class, 'index'])
        ->name('leave-status.index');

    $router->get('leaves/{employee}/summaries-data-table', [LeaveSummeryController::class, 'summaries'])
        ->name('leave-summaries.index');

    $router->get('settings/leaves', [LeaveSettingController::class, 'index'])
        ->name('leave-settings.index');

    $router->post('settings/leaves', [LeaveSettingController::class, 'update'])
        ->name('leave-settings.update');

    $router->get('leaves/calendar', [LeaveCalendarController::class, 'index'])
        ->name('leave-calendar.index');

    $router->get('leaves/calendar-table', [LeaveCalendarController::class, 'summaries'])
        ->name('leave-calendar.index');

    $router->patch('leaves/{user_leave}/leave-type', [EmployeeLeaveAllowanceController::class, 'update'])
        ->name('employee-leave-amount.update');

//    $router->delete('leaves/{user_leave}/leave-type', [EmployeeLeaveAllowanceController::class, 'destroy'])
//        ->name('employee-leave-type.remove');

    $router->post('leaves/assign', [LeaveAssignController::class, 'store'])
        ->name('leave-request.store');
});
