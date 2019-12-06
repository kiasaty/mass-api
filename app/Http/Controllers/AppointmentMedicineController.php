<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\MedicineResource;

class AppointmentMedicineController extends Controller
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
     * Gets all appointment's medicines.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('show', $appointment);

        return MedicineResource::collection($appointment->medicines)
            ->additional(['success' => true ]);
    }

    /**
     * Add a new medicine for the appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id medicineID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $id, $appointmentID)
    {
        $this->checkMedicine($request);

        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('addOrRemoveMedicineOrExperiment', $appointment);

        $validatedInput = $this->validateInput($request);

        $appointment->medicines()->attach($id, $validatedInput);

        return new MedicineResource(
            $appointment->medicines()->find($id)
        );
    }

    /**
     * Update the medicine for the appointment.
     *
     * @todo validate if the appointment owns this medicine.
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id medicineID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('update', $appointment);

        $validatedInput = $this->validateInput($request);

        $appointment->updateMedicineOrder($id, $validatedInput);

        return new MedicineResource(
            $appointment->medicines()->find($id)
        );
    }

    /**
     * Gets an medicine
     *
     * @param  int $id medicineID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function show($id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $this->authorize('show', $appointment);

        $medicine = $appointment->medicines()->findOrFail($id);

        return new MedicineResource($medicine);
    }

    /**
     * Removes an medicine for appointment.
     *
     * @param  int $id medicineID
     * @param  int $appointmentID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $appointmentID)
    {
        $appointment = Appointment::findOrFail($appointmentID);

        $medicine = $appointment->medicines()->findOrFail($id);

        $this->authorize('addOrRemoveMedicineOrExperiment', $appointment);

        $appointment->medicines()->detach($id);
    }

    /**
     * Check medicine.
     * 
     * Check if the medicine exists,
     * or if the medicine is already added for this appointment.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function checkMedicine($request)
    {
        $this->validateRouteParameters($request, [
            'id'    => 'exists:medicines|unique:appointment_medicine,medicine_id,NULL,NULL,appointment_id,' . $request->route('appointment_id'),
        ], [
            'id.exists' => 'Selected medicine does not exist.',
            'id.unique' => 'This medicine has been already added.'
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
            'count'             => 'required|numeric',
            'doctor_order'      => 'nullable|string|min:3',
        ]);
    }
}
