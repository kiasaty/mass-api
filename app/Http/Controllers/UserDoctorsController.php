<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Resources\UserResource;

class UserDoctorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Gets a secretary or patient's doctors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $role  user's role: secretary/patient
     * @return \Illuminate\Http\Response
     */
    public function index($userRole, $userID)
    {
        $user = User::getUser($userID, $userRole);

        $this->authorize('relatedUser', $user);
        
        return UserResource::collection($user->doctors)
            ->additional(['success' => true ]);
    }

    /**
     * Gets a user's doctor.
     *
     * @param  int  $id
     * @param string $role  user's role: doctor/secretary/patient
     * @return \Illuminate\Http\Response
     */
    public function show($userRole, $userID, $doctorID)
    {
        $user = User::getUser($userID, $userRole);

        $this->authorize('relatedUser', $user);
        
        $doctor = $user->doctors()->findOrFail($doctorID);

        return new UserResource($doctor);
    }
}
