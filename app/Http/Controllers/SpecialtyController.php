<?php

namespace App\Http\Controllers;

use App\Specialty;
use Illuminate\Http\Request;
use App\Http\Resources\SpecialtyResource;

class SpecialtyController extends Controller
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
     * Gets all specialties
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $specialties = Specialty::all();

        return SpecialtyResource::collection($specialties)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new specialty
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('storeOrUpdate', Specialty::class);

        $validatedInput = $this->validateInput($request);

        $newSpecialty = Specialty::create($validatedInput);

        return new SpecialtyResource($newSpecialty);
    }

    /**
     * Gets an specialty
     *
     * @param  int $id specialtyID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $specialty = Specialty::findOrFail($id);

        return new SpecialtyResource($specialty);
    }

    /**
     * Edits an specialty
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id specialtyID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $specialty = Specialty::findOrFail($id);

        $this->authorize('storeOrUpdate', $specialty);

        $validatedInput = $this->validateInput($request);

        $specialty->update($validatedInput);

        return new SpecialtyResource($specialty);
    }

    /**
     * Deletes an specialty
     *
     * @param  int $id specialtyID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $specialty = Specialty::findOrFail($id);

        $this->authorize('storeOrUpdate', $specialty);

        $specialty->delete();
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
