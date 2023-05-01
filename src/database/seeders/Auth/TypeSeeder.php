<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Type;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
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
        Type::query()->truncate();
        Type::query()->insert([
            [
                "name" => "App",
                "alias" => "app",
            ],
            [
                "name" => "Tenant",
                "alias" => "tenant",
            ]
        ]);
    }
}
