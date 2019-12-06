<?php

namespace App\Http\Controllers;

use App\Specialty;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\SpecialtyResource;

class DoctorSpecialtyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Gets all doctor's specialties.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $doctorID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        return SpecialtyResource::collection($doctor->specialties)
            ->additional(['success' => true ]);
    }

    /**
     * Add a new specialty for the doctor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id specialtyID
     * @param  int $doctorID
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        $this->authorize('addOrRemove', Specialty::class);

        $doctor->specialties()->attach($id);
    }

    /**
     * Gets an specialty
     *
     * @param  int $id specialtyID
     * @param  int $doctorID
     * @return \Illuminate\Http\Response
     */
    public function show($id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        $specialty = $doctor->specialties()->findOrFail($id);

        return new SpecialtyResource($specialty);
    }

    /**
     * Removes an specialty for doctor.
     *
     * @param  int $id specialtyID
     * @param  int $doctorID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        $specialty = $doctor->specialties()->findOrFail($id);

        $this->authorize('addOrRemove', $specialty);

        $doctor->specialties()->detach($id);
    }
}
