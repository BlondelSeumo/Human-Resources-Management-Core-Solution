<?php

use App\Http\Controllers\Core\Auth\UserInvitationController;
use App\Http\Controllers\Core\Auth\Role\{RoleController, RolePermissionController};
use App\Http\Controllers\Core\Auth\User\{UserController,
    UserPasswordController,
    UserRoleController,
    UserUpdateController,
    UserThumbnailController};

// All route names are prefixed with 'admin.auth'.
Route::group([ 'prefix' => 'auth' ], function () {
    //user roles
    Route::post('users/attach-roles/{user}', [UserRoleController::class, 'store'])
        ->name('users.attach-roles');

    Route::post('users/detach-roles/{user}', [UserRoleController::class, 'update'])
        ->name('users.detach-roles');

    //settings
    Route::get('users/settings', [UserUpdateController::class, 'index'])
        ->name('users.settings_list');

    //profile picture
    Route::get('users/profile-picture', [UserThumbnailController::class, 'show'])
        ->name('users.profile-picture');

    Route::post('users/invite-user', [UserInvitationController::class, 'invite'])
            ->name('user.invite');

    Route::post('users/cancel-invitation/{user}', [UserInvitationController::class, 'cancel'])
        ->name('invitation.cancel-user');

    Route::resource('users', UserController::class);
    //attach/detach role
    Route::post('roles/attach-permissions/{role}', [RolePermissionController::class, 'store'])
        ->name('roles.attach-permissions');

    Route::post('roles/detach-permissions/{role}', [RolePermissionController::class, 'update'])
        ->name('roles.detach-permissions');

    Route::post('roles/{role}/attach-users', [UserRoleController::class, 'attachUsers'])
        ->name('roles.attach_users_to');

    //roles
    Route::resource('roles', RoleController::class);
});
