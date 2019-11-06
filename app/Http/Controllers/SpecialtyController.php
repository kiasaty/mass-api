<?php

namespace App\Http\Controllers;

use App\Specialty;
use Illuminate\Http\Request;
use App\Http\Resources\Specialty as SpecialtyResource;

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
        if(!$request->user() || $request->user()->isAdmin()) {
            $specialties = Specialty::all();
        } else {
            $specialties = $request->user()->specialty;
        }

        if ($request->query('q')) {
            $specialties = Specialty::where('title', 'like', "%{$searchQuery['q']}%")->get();
        }

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
        $this->authorize('store', Specialty::class);

        $validatedAttributes = $this->validateInput($request);

        $newSpecialty = Specialty::create($validatedAttributes);

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

        $this->authorize('update', $specialty);

        $specialty->update($validatedAttributes);

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

        $this->authorize('update', $specialty);

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
        ]);
    }
}
