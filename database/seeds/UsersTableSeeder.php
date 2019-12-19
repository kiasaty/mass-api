<?php

class UsersTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedAdmins();

        $this->seedDoctors();
        
        $this->seedSecretaries();

        $this->seedPatients();
    }

    /**
     *
     * @return void
     */
    public function seedAdmins()
    {
        factory(App\User::class, 'admin', 1)->create();
    }

    /**
     *
     * @return void
     */
    public function seedDoctors()
    {
        factory(App\User::class, 'doctor', 2)->create()->each(function ($user) {
            $user->workSchedules()->saveMany(
                factory(App\WorkSchedule::class, 4)->make()
            );
            $user->specialties()->save(
                factory(App\Specialty::class)->make()
            );
        });
    }

    /**
     *
     * @return void
     */
    public function seedSecretaries()
    {
        factory(App\User::class, 'secretary', 1)->create();
    }

    /**
     *
     * @return void
     */
    public function seedPatients()
    {
        factory(App\User::class, 'doctor', 5)->create()->each(function ($user) {
            $user->medicalRecord()->save(
                factory(App\MedicalRecord::class)->make()
            );
        });
    }
}