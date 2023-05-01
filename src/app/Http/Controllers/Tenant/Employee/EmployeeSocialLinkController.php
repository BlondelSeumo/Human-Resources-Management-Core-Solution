<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use Illuminate\Http\Request;

class EmployeeSocialLinkController extends Controller
{
    public function index(User $employee)
    {
        $socialLinks =  $employee->socialLinks;
        return collect(config('settings.supported_social_links'))->map(function ($link) use ($socialLinks) {
            return [
                $link => optional($socialLinks->firstWhere('key', $link))->value
            ];
        });
    }

    public function update(User $employee, Request $request)
    {
        $request->validate([
            'key' => 'required|in:'.implode(',', config('settings.supported_social_links')),
            'value' => 'nullable|url'
        ]);

        $employee->socialLinks()->updateOrCreate([
            'key' => $request->get('key')
        ], [
            'key' => $request->get('key'),
            'value' => $request->get('value')
        ]);

        return updated_responses('social_links');
    }
}
