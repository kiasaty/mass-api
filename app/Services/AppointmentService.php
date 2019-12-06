<?php

namespace App\Services;

use App\User;
use App\Appointment;

class AppointmentService
{
    /**
     * Appointment id.
     *
     * @var int
     */
    protected $appointmentID;

    /**
     * Doctor id.
     *
     * @var int
     */
    protected $doctorID;

    /**
     * Patient id.
     *
     * @var int
     */
    protected $patientID;

    /**
     * Appointment.
     *
     * @var \App\Appointment
     */
    protected $appointment;

    /**
     * The doctor associated with this appointment.
     *
     * @var \App\User
     */
    protected $doctor;

    /**
     * The patient associated with this appointment.
     *
     * @var \App\User
     */
    protected $patient;

    /**
     * Default appointment duration.
     *
     * @var const
     */
    public const DEFALUT_APPOINTMENT_DURATION = 20;

    /**
     * Create a new instance.
     * 
     * Following arguments can be passed to this service:
     *      - appointment_id    type: int
     *      - doctor_id         type: int
     *      - patient_id        type: int
     *      - appointment       type: \App\Appointment
     *      - doctor            type: \App\User
     *      - patient           type: \App\User
     *
     * @param  array  $args
     * @return void
     */
    public function __construct($args)
    {
        $this->appointmentID = $args['appointment_id'] ?? null;

        $this->doctorID = $args['doctor_id'] ?? null;

        $this->patientID = $args['patient_id'] ?? null;

        $this->appointment = $args['appointment'] ?? null;

        $this->doctor = $args['doctor'] ?? null;

        $this->patient = $args['patient'] ?? null;
    }

    /**
     * Schedule new appointment.
     *
     * @todo this needs refactoring. it also needs to advance.
     * @return \App\Appointment
     */
    public function schedule()
    {
        $doctor = $this->getDoctor();

        $startTimeForNewAppointment = $this->getDoctorNextFreeTime();

        return $doctor->appointments()->create([
            'patient_id'    => $this->patientID,
            'start_time'    => $startTimeForNewAppointment,
            'end_time'      => $this->getEndTimeByStartTime($startTimeForNewAppointment)
        ]);
    }

    /**
     * Get doctor's next free time.
     *
     * @return  timestamp
     */
    public function getDoctorNextFreeTime()
    {
        $lastAppointment = $this->getDoctorLastAppointment();

        if (is_null($lastAppointment)) {
            $workSchedule = $this->getDoctor()->workSchedules()->first();
            return $this->getFirstSessionInWorkSchedule($workSchedule);
        }

        if($this->sameWorkingTimeHasAnyFreeTime($lastAppointment)) {
            return strtotime($lastAppointment->end_time);
        }
        
        $workSchedule = $this->nextWorkingTime($lastAppointment);

        return $this->getFirstSessionInWorkSchedule($workSchedule);
    }

    /**
     * Get doctor's last appointment.
     *
     * @return \App\Appointment
     */
    public function getDoctorLastAppointment()
    {
        $doctor = $this->getDoctor();

        return $doctor->appointments()->latest('end_time')->first();
    }

    /**
     * Check if the same working schedule has any free time.
     *
     * @param  \App\Appointment $lastAppointment
     * @return bool
     */
    protected function sameWorkingTimeHasAnyFreeTime($lastAppointment)
    {
        $workingTime = $this->getWorkingTime($lastAppointment);

        $timeLeftInWorkingTime = $this->getTimeDifferenceInMinutes(
            $workingTime->end_time,
            $lastAppointment->end_time->format('H:i:s')
        );

        return $timeLeftInWorkingTime > self::DEFALUT_APPOINTMENT_DURATION;
    }

    /**
     * Get appointment's working schedule.
     *
     * @param  \App\Appointment $lastAppointment
     * @return \App\WorkSchedule
     */
    protected function getWorkingTime($lastAppointment)
    {
        $day = $lastAppointment->end_time->format('w');

        $time = $lastAppointment->end_time->format('H:i:s');

        return $this->doctor->workSchedules()
            ->where('day_of_week', $day)
            ->whereTime('start_time', '<', $time)
            ->whereTime('end_time', '>=', $time)
            ->first();
    }

    /**
     * Get appointment's next working schedule.
     *
     * @param  \App\Appointment $lastAppointment
     * @return \App\WorkSchedule
     */
    protected function nextWorkingTime($lastAppointment)
    {
        $doctor = $this->getDoctor();

        $lastAppointmentWorkScedule = $this->getWorkingTime($lastAppointment);

        $doctorWorkingSchedules = $doctor->workSchedules;

        if($doctorWorkingSchedules->count() == 1) {
            return $doctorWorkingSchedules->first();
        }

        $index = $doctorWorkingSchedules->search(function ($item, $key) use ($lastAppointmentWorkScedule) {
            return $item->id == $lastAppointmentWorkScedule->id;
        });

        $reordered = ($doctorWorkingSchedules->splice($index))->concat($doctorWorkingSchedules);

        $reordered->shift();

        return $reordered->shift();
    }

    /**
     * Get the first session in work schedule
     * 
     * @param  \App\WorkSchedule  $workSchedule
     * @return timestamp
     */
    protected function getFirstSessionInWorkSchedule($workSchedule)
    {
        $appointmentDay = getDayTitle($workSchedule->day_of_week);

        return strtotime($appointmentDay . $workSchedule->start_time);
    }

    /**
     * Get the end time for the appointment.
     *
     * @param  timestamp  $startTime
     * @return timestamp
     */
    protected function getEndTimeByStartTime($startTime)
    {
        return $startTime + 60 * self::DEFALUT_APPOINTMENT_DURATION;
    }

    /**
     * Get the time difference in minutes.
     *
     * @param  string  $timeOne
     * @param  string  $timeTwo
     * @return int
     */
    protected function getTimeDifferenceInMinutes($timeOne, $timeTwo)
    {
        $timeOne = date_create($timeOne);
        
        $timeTwo = date_create($timeTwo);

        $timeDifference = $timeTwo->diff($timeOne);

        return ($timeDifference->h * 60) + $timeDifference->i;
    }

    /**
     * Get the appointment.
     *
     * @return \App\Appointment
     */
    public function getAppointment()
    {
        if (!isset($this->appointment)) {
            $this->appointment = Appointment::find($this->appointmentID);
        }

        return $this->appointment;
    }

    /**
     * Get the doctor associated with this appointment.
     *
     * @return \App\User
     */
    public function getDoctor()
    {
        if (!isset($this->doctor)) {
            $this->doctor = User::getUser($this->doctorID, 'doctors');
        }

        return $this->doctor;
    }

    /**
     * Get the patient associated with this appointment.
     *
     * @return \App\User
     */
    public function getPatient()
    {
        if (!isset($this->patient)) {
            $this->patient = User::getUser($this->patientID, 'patients');
        }

        return $this->patient;
    }
}