<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Holiday\Holiday;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createAmount = 10;

        $makeHolidays = [];
        for ($i = 1; $i < $createAmount; $i++){
            $date = Carbon::now()->startOfYear()->addDays(rand(0, 365));
            $makeHolidays[] = [
                'name' => 'Holiday'.' '.$i,
                'start_date' => $date,
                'end_date' => $date->addDays(rand(0,2)),
                'repeats_annually' => rand(0,1),
                'tenant_id' => 1
            ];
        }

        Holiday::query()->insert($makeHolidays);
    }
}
