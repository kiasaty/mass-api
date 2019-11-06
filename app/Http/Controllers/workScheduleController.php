<?php

namespace App\Http\Controllers;

use App\workSchedule;
use Illuminate\Http\Request;
use App\Http\Resources\workSchedule as workScheduleResource;

class workScheduleController extends Controller
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
     * Gets all workSchedules
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->user() || $request->user()->isAdmin()) {
            $workSchedules = workSchedule::all();
        } else {
            $workSchedules = $request->user()->workSchedule;
        }

        if ($request->query('q')) {
            $workSchedules = workSchedule::where('title', 'like', "%{$searchQuery['q']}%")->get();
        }

        return workScheduleResource::collection($workSchedules)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new workSchedule
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store', workSchedule::class);

        $validatedAttributes = $this->validateInput($request);

        $newworkSchedule = workSchedule::create($validatedAttributes);

        return new workScheduleResource($newworkSchedule);
    }

    /**
     * Gets an workSchedule
     *
     * @param  int $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $workSchedule = workSchedule::findOrFail($id);

        return new workScheduleResource($workSchedule);
    }

    /**
     * Edits an workSchedule
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $workSchedule = workSchedule::findOrFail($id);

        $this->authorize('update', $workSchedule);

        $workSchedule->update($validatedAttributes);

        return new workScheduleResource($workSchedule);
    }

    /**
     * Deletes an workSchedule
     *
     * @param  int $id workScheduleID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $workSchedule = workSchedule::findOrFail($id);

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
            'title'         => 'required|string|min:3',
        ]);
    }
}
