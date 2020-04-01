<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function loginAsAdmin($user = null)
    {
        return $this->login('admin', $user);
    }

    protected function login($role, $user = null)
    {
        $user = $user ?: factory('App\User', $role)->create();

        $this->actingAs($user);

        return $user;
    }
}
