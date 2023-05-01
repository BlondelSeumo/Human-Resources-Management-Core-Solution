<?php


namespace App\Filters\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait SearchThroughUserFilter
{
    public function search($search = null): void
    {
        $this->builder->whereHas('user', function (Builder $builder) use ($search){
            $builder->where('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhereRaw(DB::raw('CONCAT(`first_name`, " ", `last_name`) LIKE ?'), ["%$search%"]);
        });
    }
}