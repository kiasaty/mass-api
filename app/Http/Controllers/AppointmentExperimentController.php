<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\ExperimentResource;

class AppointmentExperimentController extends Controller
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
     * Gets all appointment's experiments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('show', $appointment);

        return ExperimentResource::collection($appointment->experiments)
            ->additional(['success' => true ]);
    }

    /**
     * Add a new experiment for the appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id experimentID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $id, $appointmentID)
    {
        $this->checkExperiment($request);

        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('addOrRemoveMedicineOrExperiment', $appointment);

        $validatedInput = $this->validateInput($request);

        $appointment->experiments()->attach($id, $validatedInput);

        return new ExperimentResource(
            $appointment->experiments()->find($id)
        );
    }

    /**
     * Update the experiment for the appointment.
     *
     * @todo validate if the appointment owns this medicine.
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id experimentID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('update', $appointment);

        $validatedInput = $this->validateInput($request);

        $appointment->updateExperimentOrder($id, $validatedInput);

        return new ExperimentResource(
            $appointment->experiments()->find($id)
        );
    }

    /**
     * Gets an experiment
     *
     * @param  int $id experimentID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function show($id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('show', $appointment);

        $experiment = $appointment->experiments()->findOrFail($id);

        return new ExperimentResource($experiment);
    }

    /**
     * Removes an experiment for appointment.
     *
     * @param  int $id experimentID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $experiment = $appointment->experiments()->findOrFail($id);

        $this->authorize('addOrRemoveMedicineOrExperiment', $appointment);

        $appointment->experiments()->detach($id);
    }

    /**
     * Check experiment.
     * 
     * Check if the experiment exists,
     * or if the experiment is already added for this appointment.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function checkExperiment($request)
    {
        $this->validateRouteParameters($request, [
            'id'    => 'exists:experiments|unique:appointment_experiment,experiment_id,NULL,NULL,appointment_id,' . $request->route('appointment_id'),
        ], [
            'id.exists' => 'Selected experiment does not exist.',
            'id.unique' => 'This experiment has been already added.'
        ]);
    }

    /**
     * Validates user's input.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Array  validated input
     */
    private function validateInput($request)
    {
        return $this->validate($request, [
            'doctor_order'      => 'nullable|string|min:3',
        ]);
    }
}
