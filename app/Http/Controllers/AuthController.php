<?php

namespace App\Http\Controllers;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    /**
     * Authenticate the user and return the Token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JWT
     */
    public function authenticate(Request $request)
    {
        $credentials = $this->validateInput($request);

        $user = $this->findUser($credentials);

        $this->checkIfCredentialsAreCorrect($user, $credentials);

        $jwt = $this->generateJWT($user);

        return (new UserResource($user))
            ->additional(['token' => $jwt ]);
    }

    /**
     * Validates received credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array validated credentials
     */
    private function validateInput(Request $request)
    {
        return $this->validate($request, [
            'username'  => 'required|string',
            'password'  => 'required|string'
        ]);
    }

    /**
     * Finds the user by username
     *
     * @param  array  $credentials
     * @return \App\User
     */
    private function findUser($credentials)
    {
        return User::where('username', $credentials['username'])->first();
    }
    

    /**
     * Check if the credentials are correct.
     *
     * @param  \App\User $user
     * @param  array $credentials
     * @throws \Exception
     */
    private function checkIfCredentialsAreCorrect($user, $credentials)
    {
        if (is_null($user) || !app('hash')->check($credentials['password'], $user->password)) {
            abort(422, 'Wrong Credentials!');
        }
    }

    /**
     * Generates the Json Web Token (JWT).
     *
     * @param  \App\User  $user
     * @return string  jwt token
     */
    private function generateJWT($user)
    {
        // The application key is being used as secret key
        $key = env('APP_KEY');

        $payload = [
            // Reserved claims:
            'iss' => "MASS",                // Issuer of the token
            'sub' => $user->id,             // Subject of the token
            'iat' => time(),                // Time when JWT was issued. 
            'exp' => time() + 60 * 60 * 12, // Expiration time (12 hours)

            // Private claims:
            'role'  => $user->role
        ];
        
        return JWT::encode($payload, $key);
    }
}