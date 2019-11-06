<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }

    /**
     * Gets all doctors/secretaries/patients/admins
     *
     * @param string $role  user's role: doctor/secretary/patient/admin
     * @return \Illuminate\Http\Response
     */
    public function index($role)
    {
        $this->authorize('index', User::class);

        $users = User::getAll($role);
        
        return UserResource::collection($users)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a user
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @param string $role  user's role: doctor/secretary/patient
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $role)
    {
        $validatedAttributes = $this->validate($request, [
            'name' => 'required|string|min:3',
            // 'phone_number' => 'required|regex:/^(\+989|00989|989|09|9)\d{9}$/|unique:users',
            // 'username' => 'nullable|min:3|regex:/^\S*$/|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);
        $validatedAttributes['role'] = User::ROLES[$role];
        $validatedAttributes['password'] = app('hash')->make($validatedAttributes['password']);

        $newUser = User::create($validatedAttributes);

        return new UserResource($newUser);
    }

    /**
     * Gets a user
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @param  int  $id
     * @param string $role  user's role: doctor/secretary/patient
     * @return \Illuminate\Http\Response
     */
    public function show($id, $role)
    {
        $user = User::findUser($id, $role);

        $this->authorize('show', $user);

        return new UserResource($user);
    }

    /**
     * Edits a user's information
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @param string $role  user's role: doctor/secretary/patient
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $role)
    {
        $user = User::findUser($id, $role);

        $this->authorize('update', $user);

        $validatedAttributes = $this->validate($request, [
            'name' => 'required|string|min:3',
            // 'phone_number' => ['required', 'regex:/^(\+989|00989|989|09|9)\d{9}$/', Rule::unique('users')->ignore($user)],
            // 'username' => ['nullable', 'min:3', 'regex:/^\S*$/', Rule::unique('users')->ignore($user)],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user)],
            'password' => 'sometimes|required|min:8|confirmed'
        ]);
        if ($request->filled('password')) {
            $validatedAttributes['password'] = app('hash')->make($validatedAttributes['password']);
        }

        $user->update($validatedAttributes);

        return new UserResource($user);
    }

    /**
     * Delets a user
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @param string $role  user's role: doctor/secretary/patient
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $role)
    {
        $user = User::findUser($id, $role);

        $this->authorize('destroy', $user);

        return (string) $user->delete();
    }
}
