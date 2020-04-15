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
            "username" => "Haskabab",
            "email" => "verheijen.webdevelopment@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);

        $henk = User::create([
            "username" => "Putin",
            "email" => "henk@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);

        $bob = User::create([
            "username" => "Stalin",
            "email" => "bob@gmail.com",
            "password" => bcrypt("engeland"),
            "is_admin" => true,
        ]);

        $test = User::create([
            "username" => "Testman",
            "email" => "test@test.nl",
            "password" => bcrypt("engeland"),
        ]);
    }
}
