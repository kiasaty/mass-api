<?php

class SpecialtiesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Specialty::class, 10)->create();
    }
}