<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

class BeneficiaryBadgeFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter;
    use MakeArrayFromString;

    public function status($value = null)
    {
        if ($value == 'active'){
            $active = 1;
        }elseif ($value == 'inactive'){
            $active = 0;
        }else{
            $active = null;
        }

        $this->builder->when($active !== null, function (Builder $builder) use ($active) {
            $builder->where('is_active', $active);
        });
    }
    public function type($type = null)
    {
        $types = $this->makeArray($type);

        $this->builder->when(count($types), function (Builder $builder) use ($types) {
            $builder->whereIn('type', $types);
        });
    }
}