<?php

namespace App\Http\Controllers;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Athenticate the user and return the Token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JWT
     */
    public function authenticate(Request $request)
    {
        $credentials = $this->validateCredentials($request);

        $user = $this->findUser($credentials);
        
        if (is_null($user) || $this->credentialsAreNotValid($user, $credentials)) {
            return $this->credentialsAreNotValidResponse($request);
        }

        $jwt = $this->generateJWT($user);

        return response()->json([
            'success'   => true,
            'token'     => $jwt,
            'user'      => [
                'role'          => $user->role,
                'name'          => $user->first_name,
                'email'         => $user->last_name,
                'phone_number'  => $user->phone_number,
                'profile_photo' => $user->profile_photo,
            ]
        ]);
    }

    /**
     * Validates received credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array validated credentials
     */
    private function validateCredentials(Request $request)
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
     * Validates the user credentials.
     *
     * @param  \App\User $user
     * @param  array $credentials
     * @param  bool
     */
    private function credentialsAreNotValid($user, $credentials)
    {
        return ! app('hash')->check($credentials['password'], $user->password);
    }

    /**
     * Returns wrong credentials response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function credentialsAreNotValidResponse(Request $request)
    {
        abort(422, json_encode([
            'credentials' => 'Wrong Credentials!',
        ]));
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