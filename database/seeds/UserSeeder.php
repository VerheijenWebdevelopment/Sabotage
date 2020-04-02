<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->delete();

        $nick = User::create([
            "name" => "Nick Verheijen",
            "username" => "Haskabab",
            "email" => "verheijen.webdevelopment@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);

        $henk = User::create([
            "name" => "Henk de Vries",
            "username" => "De Henk Tank",
            "email" => "henk@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);
    }
}
