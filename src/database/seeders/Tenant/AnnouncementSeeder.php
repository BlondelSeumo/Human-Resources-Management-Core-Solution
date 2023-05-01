<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Employee\Announcement\Announcement;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Announcement::class, 10)->create([
            'start_date' => today()->addDays(rand(-2, 2)),
            'end_date' => today()->addDays(rand(-2, 2)),
        ]);
    }
}
