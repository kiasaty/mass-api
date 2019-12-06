<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\AppointmentResource;
use App\User;

class AppointmentController extends Controller
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
     * Gets all appointments
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $appointments = Appointment::getAppointments(
            $request->user()
        );

        return AppointmentResource::collection($appointments)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new appointment
     *
     * @todo check roles
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'doctor_id'     => ['required', 'exists:users,id', function ($attribute, $value, $fail) {
                $doctor = User::find($value);
                if ($doctor->workSchedules()->count() === 0) {
                    return $fail('Sorry! This doctor has no free time at the moment.');
                }
            }],
            'patient_id'    => 'required|exists:users,id',
        ]);

        $newAppointment = Appointment::schedule($validatedInput);

        return new AppointmentResource($newAppointment);
    }

    /**
     * Gets an appointment
     *
     * @param  int  $id  appointmentID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('show', Appointment::class);

        $appointment = Appointment::findOrFail($id);

        return new AppointmentResource($appointment);
    }

    /**
     * Edits an appointment
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  appointmentID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $this->authorize('update', $appointment);

        $validatedInput = $this->validate($request, [
            'doctor_diagnosis'  => 'required|string|min:3',
        ]);

        $appointment->update($validatedInput);

        return new AppointmentResource($appointment);
    }

    /**
     * Deletes an appointment
     *
     * @param  int  $id  appointmentID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        $this->authorize('destroy', $appointment);

        $appointment->delete();
    }
}
