<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Resources\UserResource;

class DoctorSecretariesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Gets a doctor's secretaries.
     *
     * @param  int  $doctorID
     * @return \Illuminate\Http\Response
     */
    public function index($doctorID)
    {
        $doctor = User::getUser($doctorID, 'doctors');

        $this->authorize('relatedUser', $doctor);
        
        return UserResource::collection($doctor->secretaries)
            ->additional(['success' => true ]);
    }

    /**
     * Gets a doctor's secretary.
     *
     * @param  int  $doctorID
     * @param  int  $secretaryID
     * @return \Illuminate\Http\Response
     */
    public function show($doctorID, $secretaryID)
    {
        $doctor = User::getUser($doctorID, 'doctors');

        $this->authorize('relatedUser', $doctor);
        
        $secretary = $doctor->secretaries()->findOrFail($secretaryID);

        return new UserResource($secretary);
    }
}
