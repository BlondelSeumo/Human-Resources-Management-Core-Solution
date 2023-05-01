<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Employee\Designation;
use Illuminate\Database\Seeder;

class DesignationDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = [
            'Chief Technology Officer (CTO)',
            'General Manager',
            'HR Manager',
            'Project Manager',
            'Software Engineer',
            'Technical lead engineer'
        ];

        Designation::query()->insert(array_map(function ($designation){
            return [
                'name' => $designation,
                'tenant_id' => 1,
            ];
        },$designations));
    }
}
