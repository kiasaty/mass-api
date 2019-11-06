<?php

namespace App\Http\Controllers;

use App\MedicalRecord;
use Illuminate\Http\Request;
use App\Http\Resources\MedicalRecord as MedicalRecordResource;

class MedicalRecordController extends Controller
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
     * Gets all medicalRecords
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->user() || $request->user()->isAdmin()) {
            $medicalRecords = MedicalRecord::all();
        } else {
            $medicalRecords = $request->user()->medicalRecord;
        }

        if ($request->query('q')) {
            $medicalRecords = MedicalRecord::where('title', 'like', "%{$searchQuery['q']}%")->get();
        }

        return MedicalRecordResource::collection($medicalRecords)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new medicalRecord
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store', MedicalRecord::class);

        $validatedAttributes = $this->validateInput($request);

        $newMedicalRecord = MedicalRecord::create($validatedAttributes);

        return new MedicalRecordResource($newMedicalRecord);
    }

    /**
     * Gets an medicalRecord
     *
     * @param  int $id medicalRecordID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $medicalRecord = MedicalRecord::findOrFail($id);

        return new MedicalRecordResource($medicalRecord);
    }

    /**
     * Edits an medicalRecord
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id medicalRecordID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $medicalRecord = MedicalRecord::findOrFail($id);

        $this->authorize('update', $medicalRecord);

        $medicalRecord->update($validatedAttributes);

        return new MedicalRecordResource($medicalRecord);
    }

    /**
     * Deletes an medicalRecord
     *
     * @param  int $id medicalRecordID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medicalRecord = MedicalRecord::findOrFail($id);

        $this->authorize('update', $medicalRecord);

        $medicalRecord->delete();
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
