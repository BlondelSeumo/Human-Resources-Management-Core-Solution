<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Utility\Comment;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LeaveSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();
        Leave::query()->truncate();
        $now = now()->subDays(15);

        factory(Leave::class, 10)->create()->each(function (Leave $leave, $index) use ($now) {
            if ($leave->duration_type === 'multi_day') {
                return $leave->update([
                    'start_at' => $now,
                    'end_at' => Carbon::parse($now)->addDays(rand(1, 4))
                ]);
            }

            $now->addDay();

            return $leave->update([
                'start_at' => Carbon::parse($now),
                'end_at' => Carbon::parse($now)->addHours(rand(1, 9))
            ]);
        });

        $faker = resolve(\Faker\Generator::class);

        Leave::get()
            ->map(function ($leave) use ($faker) {
                $leave->comments()->save(new Comment([
                    'user_id' => $leave->user->id,
                    'type' => 'reason-note',
                    'comment' => $faker->sentence(10)
                ]));
            });

        $this->enableForeignKeys();
    }
}
