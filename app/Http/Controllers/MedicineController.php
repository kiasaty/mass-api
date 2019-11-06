<?php

namespace App\Http\Controllers;

use App\Medicine;
use Illuminate\Http\Request;
use App\Http\Resources\Medicine as MedicineResource;

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
     * Gets all medicines
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->user() || $request->user()->isAdmin()) {
            $medicines = Medicine::all();
        } else {
            $medicines = $request->user()->medicine;
        }

        if ($request->query('q')) {
            $medicines = Medicine::where('title', 'like', "%{$searchQuery['q']}%")->get();
        }

        return MedicineResource::collection($medicines)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a new medicine
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('store', Medicine::class);

        $validatedAttributes = $this->validateInput($request);

        $newMedicine = Medicine::create($validatedAttributes);

        return new MedicineResource($newMedicine);
    }

    /**
     * Gets an medicine
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
     * Edits an medicine
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id medicineID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $this->authorize('update', $medicine);

        $medicine->update($validatedAttributes);

        return new MedicineResource($medicine);
    }

    /**
     * Deletes an medicine
     *
     * @param  int $id medicineID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        $this->authorize('update', $medicine);

        $medicine->delete();
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
