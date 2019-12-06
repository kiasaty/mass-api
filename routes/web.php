<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "Welcome to MASS! <br /> Medical Appointment Schedule System";
});

$router->post('/login', 'AuthController@authenticate');

$router->group(['prefix' => 'appointments'], function () use ($router) {
    $router->get('/', 'AppointmentController@index');
    $router->post('/', 'AppointmentController@store');
    $router->get('/{id}', 'AppointmentController@show');
    $router->put('/{id}', 'AppointmentController@update');
    $router->delete('/{id}', 'AppointmentController@destroy');
});

$router->group(['prefix' => 'appointments/{appointment_id}/medicines'], function () use ($router) {
    $router->get('/', 'AppointmentMedicineController@index');
    $router->put('/{id}', 'AppointmentMedicineController@add');
    $router->patch('/{id}', 'AppointmentMedicineController@update');
    $router->get('/{id}', 'AppointmentMedicineController@show');
    $router->delete('/{id}', 'AppointmentMedicineController@destroy');
});

$router->group(['prefix' => 'appointments/{appointment_id}/experiments'], function () use ($router) {
    $router->get('/', 'AppointmentExperimentController@index');
    $router->put('/{id}', 'AppointmentExperimentController@add');
    $router->patch('/{id}', 'AppointmentExperimentController@update');
    $router->get('/{id}', 'AppointmentExperimentController@show');
    $router->delete('/{id}', 'AppointmentExperimentController@destroy');
});

$router->group(['prefix' => 'experiments'], function () use ($router) {
    $router->get('/', 'ExperimentController@index');
    $router->post('/', 'ExperimentController@store');
    $router->get('/{id}', 'ExperimentController@show');
    $router->put('/{id}', 'ExperimentController@update');
    $router->delete('/{id}', 'ExperimentController@destroy');
});

// $router->group(['prefix' => 'medicalrecords'], function () use ($router) {
//     $router->get('/', 'MedicalRecordController@index');
//     $router->post('/', 'MedicalRecordController@store');
//     $router->get('/{id}', 'MedicalRecordController@show');
//     $router->put('/{id}', 'MedicalRecordController@update');
//     $router->delete('/{id}', 'MedicalRecordController@destroy');
// });

$router->group(['prefix' => 'medicines'], function () use ($router) {
    $router->get('/', 'MedicineController@index');
    $router->post('/', 'MedicineController@store');
    $router->get('/{id}', 'MedicineController@show');
    $router->put('/{id}', 'MedicineController@update');
    $router->delete('/{id}', 'MedicineController@destroy');
});

$router->group(['prefix' => 'specialties'], function () use ($router) {
    $router->get('/', 'SpecialtyController@index');
    $router->post('/', 'SpecialtyController@store');
    $router->get('/{id}', 'SpecialtyController@show');
    $router->put('/{id}', 'SpecialtyController@update');
    $router->delete('/{id}', 'SpecialtyController@destroy');
});

$router->group(['prefix' => '{role:admins|doctors|secretaries|patients}'], function () use ($router) {
    $router->get('/', 'UserController@index');
    $router->post('/', 'UserController@store');
    $router->get('/{id}', 'UserController@show');
    $router->put('/{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@destroy');
});

$router->group(['prefix' => 'patients/{patient_id}'], function () use ($router) {
    $router->get('/medicalrecord', 'MedicalRecord@show');
});

$router->group(['prefix' => '{role:secretaries|patients}/{id}'], function () use ($router) {
    $router->get('/doctors', 'UserDoctorsController@index');
    $router->get('/doctors/{doctor_id}', 'UserDoctorsController@show');
});

$router->group(['prefix' => 'doctors/{doctor_id}/workschedules'], function () use ($router) {
    $router->get('/', 'WorkScheduleController@index');
    $router->post('/', 'WorkScheduleController@store');
    $router->get('/{id}', 'WorkScheduleController@show');
    $router->put('/{id}', 'WorkScheduleController@update');
    $router->delete('/{id}', 'WorkScheduleController@destroy');
});

$router->group(['prefix' => 'doctors/{doctor_id}/specialties'], function () use ($router) {
    $router->get('/', 'DoctorSpecialtyController@index');
    $router->put('/{id}', 'DoctorSpecialtyController@add');
    $router->get('/{id}', 'DoctorSpecialtyController@show');
    $router->delete('/{id}', 'DoctorSpecialtyController@destroy');
});

$router->group(['prefix' => '{doctors}/{doctor_id}'], function () use ($router) {
    $router->get('/secretaries', 'DoctorSecretariesController@index');
    $router->get('/secretaries/{secretary_id}', 'DoctorSecretariesController@show');
});