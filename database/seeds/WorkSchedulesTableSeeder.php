<?php

class WorkSchedulesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\WorkSchedule::class, 10)->create();
    }
}