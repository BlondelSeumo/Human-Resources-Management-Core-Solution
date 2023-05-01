<?php

namespace Tests\Unit;

use App\Models\Tenant\Leave\LeaveType;
use App\Services\Tenant\Leave\LeaveTypeService;
use Tests\TestCase;

class LeaveTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginAsAdmin();
    }

    /** @test */
    public function it_will_check_if_service_can_create_leave_type()
    {
        $result = $this->leaveType();

        $this->assertInstanceOf(LeaveType::class, $result);
    }

    /** @test */
    public function it_will_chek_if_service_can_update_leave_type()
    {
        $result = resolve(LeaveTypeService::class)
            ->setAttributes(['name' => 'Updated', 'type' => 'paid', 'tenant_id' => 1, 'amount' => 5])
            ->validate()
            ->setModel($this->leaveType())
            ->save();

        $this->assertEquals('Updated', $result->name);

    }

    /** @test */
    public function it_will_check_if_service_can_delete_leave_type()
    {
        $this->leaveType()->delete();

        $this->assertTrue(true);
    }

    public function leaveType()
    {
        return resolve(LeaveTypeService::class)
            ->setAttributes(['name' => 'demo', 'type' => 'paid', 'tenant_id' => 1, 'amount' => 5])
            ->validate()
            ->save();
    }


}
