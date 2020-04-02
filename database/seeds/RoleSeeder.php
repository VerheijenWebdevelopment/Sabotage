<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("roles")->delete();

        $saboteur = Role::create([
            "name" => "saboteur",
            "label" => "Saboteur",
        ]);

        $kabouter = Role::create([
            "name" => "digger",
            "label" => "Golddigger"
        ]);
    }
}
