<?php
namespace Database\Seeders\Builder;

use App\Models\Core\Builder\Form\CustomFieldType;
use Illuminate\Database\Seeder;

class CustomFieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $field_types = [
            ['name' => 'text'],
            ['name' => 'textarea'],
            ['name' => 'radio'],
            ['name' => 'select'],
            ['name' => 'date'],
            ['name' => 'number'],
        ];

        CustomFieldType::query()->insert($field_types);
    }
}
