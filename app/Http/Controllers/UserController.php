<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }

    /**
     * Gets all doctors/secretaries/patients/admins
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $role  user's role: doctor/secretary/patient/admin
     * @return \Illuminate\Http\Response
     */
    public function index($role)
    {
        $this->authorize('index' . ucfirst($role), User::class);

        $users = User::getAll($role);
        
        return UserResource::collection($users)
            ->additional(['success' => true ]);
    }

    /**
     * Creates a user
     * 
     * User might be a doctor or secretary or patient or admin
     * 
     * @todo validate the phone number
     * @todo no spaces allwed in the username
     * @param string $role  user's role: doctor/secretary/patient
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $role)
    {
        if ($role !== 'patients') {
            $this->authorize('store', User::class);
        }
        
        $validatedInput = $this->validateInput($request);

        $validatedInput['role_id'] = User::getRoleID($role);
        $validatedInput['password'] = app('hash')->make($validatedInput['password']);

        if ($request->filled('profile_photo')) {
            $validatedInput['profile_photo'] = $this->saveProfilePhoto($request);
        }

        $newUser = User::create($validatedInput);

        if ($newUser->isPatient()) {
            $newUser->generateMedicalRecordNumberForPatient();
        }

        return new UserResource($newUser);
    }

    /**
     * Gets a user
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @param  int  $id
     * @param string $role  user's role: doctor/secretary/patient
     * @return \Illuminate\Http\Response
     */
    public function show($id, $role)
    {
        $user = User::getUser($id, $role);
        
        $this->authorize('show', $user);

        return new UserResource($user);
    }

    /**
     * Edits a user's information
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @todo DRY: use one method to validate the user input in store and update
     * @todo if the user has updoaded a new profile photo, remove the old one form the iamge folder
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @param string $role  user's role: doctor/secretary/patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $role)
    {
        $user = User::getUser($id, $role);

        $this->authorize('update', $user);

        $validatedInput = $this->validateInput($request, $user);

        if ($request->filled('password')) {
            $validatedInput['password'] = app('hash')->make($validatedInput['password']);
        }

        if ($request->filled('profile_photo')) {
            $validatedInput['profile_photo'] = $this->saveProfilePhoto($request);
        }

        $user->update($validatedInput);

        return new UserResource($user);
    }

    /**
     * Deletes a user
     * 
     * User might be a doctor or secretary or patient or admin
     *
     * @todo also remove the photo form image folder. the same functinality is needed in update
     * @param string $role  user's role: doctor/secretary/patient
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $role)
    {
        $user = User::getUser($id, $role);

        $this->authorize('destroy', $user);

        $user->delete();
    }

    /**
     * Saves the profile photo.
     * 
     * Saves the profile photo in the /images/profiles folder and returns the file path.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string  profile photo path
     */
    public function saveProfilePhoto(Request $request)
    {
        $image = $request['profile_photo'];

        $imageFileExtention =   $image->guessExtension()        ?? 
                                $image->guessClientExtension()  ?? 
                                $image->getClientOriginalExtension();
        
        $imageFileName = $request['lastname'] . '-' . time() . '.' . $imageFileExtention;
        
        $destinationFolder = app()->basePath('public') . "/images/profiles";

        $isProfilePhotoSaved = $image->move($destinationFolder, $imageFileName);

        if ($isProfilePhotoSaved) {
            return "images/profiles/$imageFileName";
        }
    }

    /**
     * Validates user's input
     * 
     * @todo make use of this method in validation. -test this before using
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return Array  validated input
     */
    private function validateInput($request, $user = null)
    {
        $rules = [
            'firstname'     => ['required', 'string',' min:3', 'regex:/^[A-Za-z]+$/'],
            'lastname'      => ['required', 'string', 'min:3', 'regex:/^[A-Za-z]+$/'],
            'phone_number'  => ['required', 'regex:/^(\+989|00989|989|09|9)\d{9}$/' , 'unique:users'],
            'username'      => ['required', 'string', 'min:3', 'regex:/^\S*$/', 'unique:users'],
            'password'      => ['required', 'min:4', 'confirmed'],
            'profile_photo' => ['nullable'],
            'profile_photo' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:5120']
        ];

        if ($request->isMethod('put')) {

            $rules['phone_number'][2]  = Rule::unique('users')->ignore($user);
            $rules['username'][4]      = Rule::unique('users')->ignore($user);
            
            foreach($rules as $key => $rule) {
                $index = array_search("required", $rule);
                if ($index !== false) {
                    $rules[$key][$index] = 'sometimes';
                }
            }
            
        }

        return $this->validate($request, $rules);
    }
}
