<?php


namespace App\Services\Tenant;


use App\Services\Core\BaseService;

/**
 * @method \Illuminate\Database\Eloquent\Builder newModelQuery()
 * @method \Illuminate\Database\Eloquent\Builder newQuery()
 * @method \Illuminate\Database\Eloquent\Builder query()
 * @method \Illuminate\Database\Eloquent\Builder whereCreatedAt($value)
 * @method \Illuminate\Database\Eloquent\Builder whereId($value)
 * @method \Illuminate\Database\Eloquent\Builder wherePassword($value)
 * @method \Illuminate\Database\Eloquent\Builder whereUpdatedAt($value)
 * @method \Illuminate\Database\Eloquent\Builder whereUserId($value)
 * @method \Illuminate\Database\Eloquent\Builder filters(\App\Filters\FilterBuilder $filter)
 */
class TenantService extends BaseService
{
    protected bool $refreshMemoization = false;

    public function delete(): TenantService
    {
        $this->model->delete();

        return $this;
    }


    public function setRefreshMemoization(bool $refreshMemoization): TenantService
    {
        $this->refreshMemoization = $refreshMemoization;

        return $this;
    }
}
