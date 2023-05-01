<?php

namespace Tests\Unit;

use App\Models\Tenant\Leave\LeavePeriod;
use App\Services\Tenant\Leave\LeavePeriodService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LeavePeriodTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginAsAdmin();
    }

    /** @test */
    public function it_will_check_if_service_can_create_leave_period()
    {
        $this->assertInstanceOf(LeavePeriod::class, $this->leavePeriod());
    }

    /** @test */
    public function it_will_check_if_service_can_update_leave_period()
    {
        $result = resolve(LeavePeriodService::class)
            ->setAttributes(['start_date' => '2020-12-14', 'end_date' => now()->addYear(), 'tenant_id' => 1])
            ->validate()
            ->setModel($this->leavePeriod())
            ->save();

        $this->assertInstanceOf(Carbon::class, $result->start_date);
        $this->assertEquals('2020-12-14', $result->start_date->format('Y-m-d'));
    }

    public function leavePeriod()
    {
        return resolve(LeavePeriodService::class)
            ->setAttributes(['start_date' => now(), 'end_date' => now()->addYear(), 'tenant_id' => 1])
            ->validate()
            ->save();
    }
}
