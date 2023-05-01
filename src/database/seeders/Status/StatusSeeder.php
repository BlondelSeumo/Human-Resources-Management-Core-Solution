<?php
namespace Database\Seeders\Status;

use App\Models\Core\Status;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        Status::query()->truncate();
        $statuses = [
            [
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success'
            ],
            [
                'name' => 'status_inactive',
                'type' => 'user',
                'class' => 'danger'
            ],
            [
                'name' => 'status_invited',
                'type' => 'user',
                'class' => 'warning'
            ],
            [
                'name' => 'status_active',
                'type' => 'department',
                'class' => 'primary'
            ],
            [
                'name' => 'status_inactive',
                'type' => 'department',
                'class' => 'danger'
            ],
            [
                'name' => 'status_pending',
                'type' => 'attendance',
                'class' => 'warning'
            ],
            [
                'name' => 'status_approve',
                'type' => 'attendance',
                'class' => 'success'
            ],
            [
                'name' => 'status_reject',
                'type' => 'attendance',
                'class' => 'danger'
            ],
            [
                'name' => 'status_log',
                'type' => 'attendance',
                'class' => 'secondary'
            ],
            [
                'name' => 'status_cancel',
                'type' => 'attendance',
                'class' => 'dark'
            ],
            [
                'name' => 'status_pending',
                'type' => 'leave',
                'class' => 'warning'
            ],
            [
                'name' => 'status_approved',
                'type' => 'leave',
                'class' => 'success'
            ],
            [
                'name' => 'status_rejected',
                'type' => 'leave',
                'class' => 'danger'
            ],
            [
                'name' => 'status_bypassed',
                'type' => 'leave',
                'class' => 'secondary'
            ],
            [
                'name' => 'status_canceled',
                'type' => 'leave',
                'class' => 'dark'
            ],
            [
                'name' => 'status_generated',
                'type' => 'payrun',
                'class' => 'secondary'
            ],
            [
                'name' => 'status_sent',
                'type' => 'payrun',
                'class' => 'success'
            ],
            [
                'name' => 'status_partially',
                'type' => 'payrun',
                'class' => 'warning'
            ],
            [
                'name' => 'status_generated',
                'type' => 'payslip',
                'class' => 'warning'
            ],
            [
                'name' => 'status_sent',
                'type' => 'payslip',
                'class' => 'success'
            ],
            [
                'name' => 'status_pending',
                'type' => 'payslip',
                'class' => 'secondary'
            ],
        ];

        Status::query()->insert($statuses);

        $this->enableForeignKeys();
    }
}
