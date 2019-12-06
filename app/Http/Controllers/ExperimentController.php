<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;
use App\Http\Resources\ExperimentResource;

class ExperimentController extends Controller
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
     * Gets all experiments
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $experiments = Experiment::all();

        return ExperimentResource::collection($experiments)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new experiment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('storeOrUpdate', Experiment::class);

        $validatedInput = $this->validateInput($request);

        $newExperiment = Experiment::create($validatedInput);

        return new ExperimentResource($newExperiment);
    }

    /**
     * Gets an experiment
     *
     * @param  int $id  experimentID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $experiment = Experiment::findOrFail($id);

        return new ExperimentResource($experiment);
    }

    /**
     * Edits an experiment
     *
     * @param  int  $id  experimentID
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $experiment = Experiment::findOrFail($id);

        $this->authorize('storeOrUpdate', $experiment);

        $validatedInput = $this->validateInput($request);

        $experiment->update($validatedInput);

        return new ExperimentResource($experiment);
    }

    /**
     * Deletes an experiment
     *
     * @param  int  $id experimentID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $experiment = Experiment::findOrFail($id);

        $this->authorize('storeOrUpdate', $experiment);

        $experiment->delete();
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
            'description'   => 'required|string|min:3',
        ]);
    }
}
