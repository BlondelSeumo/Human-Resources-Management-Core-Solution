<?php


use App\Http\Controllers\Tenant\User\UserEmployeeController;
use App\Http\Controllers\Core\Auth\User\UserRoleController;
use App\Http\Controllers\Core\Auth\UserInvitationController;
use App\Http\Controllers\Tenant\Auth\TenantRoleController;
use App\Http\Controllers\Tenant\Auth\TenantUserController;

Route::resource('users', TenantUserController::class)->middleware('check_behavior');
Route::resource('roles', TenantRoleController::class);

Route::post('users/attach-roles/{user}', [UserRoleController::class, 'store'])
    ->name('users.attach-roles');

Route::post('roles/{role}/attach-users', [UserRoleController::class, 'attachUsers'])
    ->name('roles.attach_users_to');

Route::post('users/detach-roles/{user}', [UserRoleController::class, 'update'])
    ->name('users.detach-roles');

Route::post('users/cancel-invitation/{user}', [UserInvitationController::class, 'cancel'])
    ->name('invitation.cancel-user');

Route::patch('users/{user}/add-to-employee', [UserEmployeeController::class, 'addToEmployee'])
    ->name('employees.add-user-to');

Route::patch('users/{user}/remove-from-employee', [UserEmployeeController::class, 'removeFromEmployee'])
    ->name('employees.remove-user-from');
