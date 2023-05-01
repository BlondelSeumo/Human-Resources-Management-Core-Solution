<?php


namespace App\Models\Tenant\Leave;


use App\Models\Tenant\Leave\Boot\LeaveCategoryBoot;
use App\Models\Tenant\TenantModel;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Leave\LeaveService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends TenantModel
{
    use LeaveCategoryBoot {
        boot as public traitBoot;
    }

    protected $fillable = [
        'name', 'alias', 'type', 'amount', 'special_percentage', 'tenant_id', 'is_enabled', 'is_earning_enabled'
    ];

    public static array $types = ['paid', 'unpaid', 'special'];

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function userLeaves(): HasMany
    {
        return $this->hasMany(UserLeave::class);
    }

    public function hasLeave($expect = null): int
    {
        return $this->leaves()
            ->when($expect, function (Builder $builder) use ($expect) {
                $builder->when(
                    $expect instanceof LeaveType,
                    fn(Builder $bl) => $bl->where('leave_type_id', '!=', $expect->id),
                    fn(Builder $bl) => $bl->where('leave_type_id', '!=', $expect),
                );
            }, function (Builder $builder) {
                $builder->where('leave_type_id', '!=', resolve(StatusRepository::class)->leaveRejected());
            })->count();
    }

    public static function boot()
    {
        self::traitBoot();
    }

}
