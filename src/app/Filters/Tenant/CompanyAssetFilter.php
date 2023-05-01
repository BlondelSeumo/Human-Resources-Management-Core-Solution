<?php

namespace App\Filters\Tenant;

use App\Filters\FilterBuilder;
use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;

class CompanyAssetFilter extends FilterBuilder
{

    use MakeArrayFromString;

    public function search($search = null)
    {
        $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where('name', 'LIKE', "%$search%")
                ->orWhere('code', 'LIKE', "%{$search}%")
                ->orWhere('serial_number', 'LIKE', "%$search%");
        });
    }

    public function userId($id = null)
    {
        if ($id) {
            $this->whereClause('user_id', $id, "=");
        }
    }

    public function assetType($ids = null)
    {
        $type_id = $this->makeArray($ids);

        $this->builder->when($type_id, function (Builder $builder) use ($type_id) {
            $builder->whereIn('type_id', $type_id);
        });
    }
}