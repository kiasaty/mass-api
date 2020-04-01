<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class SpecialtyTest extends TestCase
{
    /** @test */
    public function guests_or_users_cannot_manage_specialties()
    {
        $this->assertCanNotManageSpecialties();

        $this->loginAsDoctor()->assertCanNotManageSpecialties();

        $this->loginAsSecretary()->assertCanNotManageSpecialties();

        $this->loginAsPatient()->assertCanNotManageSpecialties();
    }

    /** @test */
    public function admins_can_get_specialties()
    {
        $this->loginAsAdmin();

        $specialty = factory('App\Specialty')->create();

        $this->get($specialty->path())->assertResponseOk();

        $this->get('/specialties')->assertResponseOk();
    }

    /**
     * @test
     */
    public function admins_can_create_a_specialty()
    {
        $this->loginAsAdmin();

        $specialty = factory('App\Specialty')->raw();

        $this->post('/specialties', $specialty);

        $this->assertResponseOk();
        $this->seeInDatabase('specialties', $specialty);
    }

    /** @test */
    public function admins_can_update_a_specialty()
    {
        $this->loginAsAdmin();

        $specialty = factory('App\Specialty')->create();

        $attributes = ['title' => 'Changed', 'description' => 'Changed'];

        $this->put($specialty->path(), $attributes);

        $this->assertResponseOk();
        $this->seeInDatabase('specialties', $attributes);
    }

    /** @test */
    public function admins_can_delete_a_specialty()
    {
        $this->loginAsAdmin();

        $specialty = factory('App\Specialty')->create();

        $this->delete($specialty->path());

        $this->assertResponseOk();
        $this->notSeeInDatabase('specialties', ['id' => $specialty->id]);
    }

    /** @test */
    public function a_specialty_requires_a_title()
    {
        $this->loginAsAdmin();

        $attributes = factory('App\Specialty')->raw(['title' => '']);

        $this->post('/specialties', $attributes);

        $this->assertResponseStatus(422);
    }

    /** @test */
    public function a_specialty_does_not_require_a_description()
    {
        $this->loginAsAdmin();

        $attributes = factory('App\Specialty')->raw(['description' => '']);

        $this->post('/specialties', $attributes);

        $this->assertResponseOk();
    }

    public function assertCanNotManageSpecialties()
    {
        $this->post('/specialties', factory('App\Specialty')->raw())->assertResponseStatus(403);
        
        $specialty = factory('App\Specialty')->create();

        $this->get('/specialties')->assertResponseStatus(403);
        $this->get($specialty->path())->assertResponseStatus(403);
        $this->put($specialty->path(), $specialty->toArray())->assertResponseStatus(403);
        $this->delete($specialty->path())->assertResponseStatus(403);
    }
}
