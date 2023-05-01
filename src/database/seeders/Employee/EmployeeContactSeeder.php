<?php /** @noinspection ALL */

namespace Database\Seeders\Employee;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\UserContact;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class EmployeeContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = resolve(Faker::class);

        User::all()->each(function (User $user) use ($faker) {
            $user->addresses()->saveMany([
                new UserContact([
                    'key' => 'present_address',
                    'value' => json_encode([
                        'country' => ucfirst($faker->country),
                        'details' => $faker->address,
                        'area' => $faker->randomElement(['LA', 'CA', 'BA']),
                        'city' => $faker->city,
                        'state' => $faker->state,
                        'zip_code' => $faker->areaCode,
                        'phone_number' => $faker->phoneNumber
                    ])
                ]),
                new UserContact([
                    'key' => 'permanent_address',
                    'value' => json_encode([
                        'country' => ucfirst($faker->country),
                        'details' => $faker->address,
                        'area' => $faker->randomElement(['LA', 'CA', 'BA']),
                        'city' => $faker->city,
                        'state' => $faker->state,
                        'zip_code' => $faker->areaCode,
                        'phone_number' => $faker->phoneNumber
                    ])
                ])
            ]);

            $user->contacts()->saveMany([
                new UserContact([
                    'key' => 'emergency_contacts',
                    'value' => json_encode([
                        'country' => $faker->country,
                        'details' => $faker->address,
                        'area' => $faker->randomElement(['LA', 'CA', 'BA']),
                        'email' => $faker->safeEmail(),
                        'name' => $faker->name,
                        'phone_number' => $faker->phoneNumber,
                        'relationship' => $faker->randomElement(['Wife', 'Father', 'Husband', 'Mother', 'Sister'])
                    ])
                ]),
                new UserContact([
                    'key' => 'emergency_contacts',
                    'value' => json_encode([
                        'country' => $faker->country,
                        'details' => $faker->address,
                        'area' => $faker->randomElement(['LA', 'CA', 'BA']),
                        'email' => $faker->safeEmail(),
                        'name' => $faker->name,
                        'phone_number' => $faker->phoneNumber,
                        'relationship' => $faker->randomElement(['Wife', 'Father', 'Husband', 'Mother', 'Sister'])
                    ])
                ]),
            ]);

            $socialLinks = [
                ['name' => 'facebook', 'domain' => 'http://facebook.com'],
                ['name' => 'twitter', 'domain' => 'http://twitter.com'],
                ['name' => 'linkedin', 'domain' => 'http://linkedin.com'],
                ['name' => 'behance', 'domain' => 'http://behance.com'],
                ['name' => 'instagram', 'domain' => 'http://instagram.com'],
                ['name' => 'youtube', 'domain' => 'http://youtube.com'],
                ['name' => 'dribble', 'domain' => 'http://dribble.com'],
                ['name' => 'skype', 'domain' => 'http://skype.com'],
                ['name' => 'pinterest', 'domain' => 'http://pinterest.com']
            ];

            $links = collect($socialLinks)->random(4)->map(fn ($link) => new UserContact([
                'key' => $link['name'],
                'value' => $link['domain'].'/'.Str::snake($user->full_name, '.').'.'.rand(1, 100)
            ]));

            $user->socialLinks()->saveMany($links);
        });
    }
}
