<?php


namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\Announcement\Announcement;

use App\Helpers\Core\Traits\HasWhen;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementService extends TenantService
{
    use HasWhen;

    public function __construct(Announcement $announcement)
    {
        $this->model = $announcement;
    }

    public function update(): AnnouncementService
    {
        $this->model->fill($this->getAttributes())->save();

        return $this;
    }

    public function assignToDepartments($departments = []): AnnouncementService
    {
        $this->model->departments()->sync($departments);
        return $this;
    }

    public function filterForDashboard()
    {
        $department = request()->user()->department->pluck('id');
        return $this->model->where('end_date', '>=', today())
                    ->where(
                        fn(Builder $b) => $b->whereDoesntHave('departments')
                            ->orWhereHas(
                                'departments',
                                fn(Builder $b) => $b->whereIn('id', $department)
                            )
                        );
    }
}
