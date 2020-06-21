<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(NbateamsTableSeeder::class);
        //$this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        //$this->call(LeaguesTableSeeder::class);
        //$this->call(TeamsTableSeeder::class);
    }
}
