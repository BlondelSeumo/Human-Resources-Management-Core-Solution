<?php

namespace App\Http\Controllers\Core\Log;

use App\Filters\Core\ActivityLogFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Core\Log\ActivityLog;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogController extends Controller
{
    public function __construct(ActivityLogFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return ActivityLog::query()
            ->with('subject', 'causer')
            ->filters($this->filter)
            ->paginate(request()->get('per_page', 15));
    }

    public function show()
    {
        return $this->getActivity(auth()->user());
    }

    public function activities(User $user)
    {
        return $this->getActivity($user);
    }

    public function getActivity($user)
    {
        return $user->actions()
        ->with('subject')
        ->when(request()->search, function (Builder $query){
            $query->where('description', 'LIKE', "%".request()->search."%");
        })
        ->orderBy('id', 'DESC')
        ->select('description', 'properties', 'created_at', 'subject_type', 'subject_id')
        ->paginate(request('per_page', 10));
    }

}
