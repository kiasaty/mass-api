<?php

namespace App\Http\Controllers;

use App\Medicine;
use Illuminate\Http\Request;
use App\Http\Resources\MedicineResource;

class MedicineController extends Controller
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
     * Gets all medicines.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $medicines = Medicine::all();

        return MedicineResource::collection($medicines)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('storeOrUpdate', Medicine::class);

        $validatedInput = $this->validateInput($request);

        $newMedicine = Medicine::create($validatedInput);

        return new MedicineResource($newMedicine);
    }

    /**
     * Gets a medicine.
     *
     * @param  int $id medicineID
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);

        return new MedicineResource($medicine);
    }

    /**
     * Edits a medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id medicineID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $this->authorize('storeOrUpdate', $medicine);

        $validatedInput = $this->validateInput($request);

        $medicine->update($validatedInput);

        return new MedicineResource($medicine);
    }

    /**
     * Deletes a medicine.
     *
     * @param  int $id medicineID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        $this->authorize('storeOrUpdate', $medicine);

        $medicine->delete();
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
            'title'         => 'required|string|min:3',
            'description'   => 'required|string|min:3',
        ]);
    }
}
