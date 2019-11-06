<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\Appointment as AppointmentResource;

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
        if(!$request->user() || $request->user()->isAdmin()) {
            $appointments = Appointment::all();
        } else {
            $appointments = $request->user()->appointment;
        }

        if ($request->query('q')) {
            $appointments = Appointment::where('title', 'like', "%{$searchQuery['q']}%")->get();
        }

        return AppointmentResource::collection($appointments)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new appointment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store', Appointment::class);

        $validatedAttributes = $this->validateInput($request);

        $newAppointment = Appointment::create($validatedAttributes);

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

        $appointment->update($validatedAttributes);

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

        $this->authorize('update', $appointment);

        $appointment->delete();
    }

    /**
     * Validates user's input
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Array  validated input
     */
    private function validateInput($request)
    {
        return $this->validate($request, [
            'title'         => 'required|string|min:3',
        ]);
    }
}
