<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Firebase\JWT\JWT;
use Exception;
use Firebase\JWT\ExpiredException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {

            if ($token = $request->header('Authorization')) {

                if (preg_match('/^Bearer\s(.+\..+\..+)$/', $token, $matches)) {
                    $jwt = $matches[1];
                } else {
                    return null;
                }
                
                $key = env('APP_KEY');

                try {
                    $payload = JWT::decode($jwt, $key, array('HS256'));
                } catch (ExpiredException $e) {
                    abort(401, $e->getMessage());
                } catch (Exception $e) {
                    return null;
                }

                $userID = $payload->sub;

                return User::find($userID);
            }
        });
    }
}
