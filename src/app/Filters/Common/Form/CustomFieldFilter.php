<?php


namespace App\Filters\Common\Form;


use App\Filters\Common\FilterContact;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldFilter extends FilterContact
{

    function filter(): Builder
    {
        return $this->query->when(optional(tenant())->id, function (Builder $builder) {
            $builder->where('tenant_id', optional(tenant())->id);
        }, function (Builder $builder) {
            $builder->whereNull('tenant_id');
        });
    }
}
