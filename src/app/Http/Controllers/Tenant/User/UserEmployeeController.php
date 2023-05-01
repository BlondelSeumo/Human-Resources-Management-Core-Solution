<?php

namespace App\Http\Controllers\Tenant\User;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;

class UserEmployeeController extends Controller
{
    public function addToEmployee(User $user)
    {
        $user->fill([
            'is_in_employee' => 1
        ])->save();

        return response()->json([
            'status' => true,
            'message' => __t('user_added_to_employee')
        ]);
    }

    public function removeFromEmployee(User $user)
    {
        $user->fill([
            'is_in_employee' => 0
        ])->save();

        return response()->json([
            'status' => true,
            'message' => __t('user_removed_from_employee')
        ]);
    }
}
