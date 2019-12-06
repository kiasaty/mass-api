<?php

namespace App\Http\Controllers;

use App\User;
use App\WorkSchedule;
use Illuminate\Http\Request;
use App\Http\Resources\WorkScheduleResource;

class WorkScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * Gets all workSchedules
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $doctorID
     * @return \Illuminate\Http\Response
     */
    public function index($doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        return WorkScheduleResource::collection($doctor->workSchedules)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new workSchedule
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $doctorID
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        $this->authorize('store', WorkSchedule::class);

        $validatedInput = $this->validateInput($request);

        $newWorkSchedule = $doctor->workSchedules()->create($validatedInput);

        return new WorkScheduleResource($newWorkSchedule);
    }

    /**
     * Gets an workSchedule
     *
     * @param  int  $doctorID
     * @param  int  $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function show($id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);

        $workSchedule = $doctor->workSchedules()->findOrFail($id);

        return new WorkScheduleResource($workSchedule);
    }

    /**
     * Edits an workSchedule
     *
     * @param \Illuminate\Http\Request  $request
     * @param  int  $doctorID
     * @param  int  $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);
        
        $workSchedule = $doctor->workSchedules()->findOrFail($id);

        $this->authorize('update', $workSchedule);

        $validatedInput = $this->validateInput($request);

        $workSchedule->update($validatedInput);

        return new WorkScheduleResource($workSchedule);
    }

    /**
     * Deletes an workSchedule
     *
     * @param  int  $doctorID
     * @param  int  $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $doctorID)
    {
        $doctor = User::findOrFail($doctorID);
        
        $workSchedule = $doctor->workSchedules()->findOrFail($id);

        $this->authorize('update', $workSchedule);

        $workSchedule->delete();
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
            'day_of_week'   => 'required|digits_between:0,6',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ]);
    }
}
