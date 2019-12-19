<?php

class ExperimentsTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Experiment::class, 10)->create();
    }
}