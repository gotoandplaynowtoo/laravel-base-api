<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $DATA_COUNT = 100;

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        \App\User::truncate();

        factory(\App\User::class, $DATA_COUNT)->create();
    }
}
