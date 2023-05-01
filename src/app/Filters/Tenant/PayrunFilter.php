<?php


namespace App\Filters\Tenant;


use App\Filters\FilterBuilder;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayrunFilter extends FilterBuilder
{
    public function created($range = null)
    {
        $period = json_decode(htmlspecialchars_decode($range), true);

        $this->builder->when($period, function (Builder $builder) use ($period) {
            $builder->where(function (Builder $builder) use($period){
                $builder->whereBetween('created_at', $period)
                    ->orWhere(DB::raw('DATE(created_at)'), $period[0]);
            });
        });
    }

    public function type($type = null)
    {
        if ($type){
            $followed = ['manual' => 'customized', 'default' => 'settings', 'employee' => 'employee'][$type];
            $this->builder->when($followed, function (Builder $builder) use ($followed) {
                $builder->where('followed', $followed);
            });
        }
    }

    public function status($status = null)
    {
        $status = 'payrun'.ucFirst(Str::camel($status));
        $status_id = resolve(StatusRepository::class)->{$status}();
        $this->builder->when($status_id, function (Builder $builder) use ($status_id) {
            $builder->where('status_id', $status_id);
        });
    }

    public function payrunPeriod($type)
    {
        $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->whereHas('payslips',function (Builder $builder) use ($type){
                $builder->where('period',"LIKE", "%{$type}%");
            });
        });
    }

    public function payrunDate($date_range)
    {
        $period = json_decode(htmlspecialchars_decode($date_range), true);

        $this->builder->when($period, function (Builder $builder) use ($period) {
            $builder->whereHas('payslips',function (Builder $builder) use ($period){
                $builder->where(function (Builder $builder) use ($period) {
                    $builder->whereBetween('start_date', $period)
                        ->orWhereBetween('end_date', array_values($period))
                        ->orWhere(function ($query) use ($period) {
                            $query->whereDate('start_date', '<=', $period['start'])
                                ->whereDate('end_date', '>=', $period['end']);
                        });
                });
            });
        });
    }

}