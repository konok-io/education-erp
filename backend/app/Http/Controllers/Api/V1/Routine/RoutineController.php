<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Routine;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Routine\RoutineResource;
use App\Services\Routine\RoutineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoutineController extends BaseController
{
    public function __construct(
        private readonly RoutineService $routineService
    ) {}

    // ===================== ROUTINE CRUD =====================

    public function index(Request $request): AnonymousResourceCollection
    {
        $routines = $this->routineService->getAll(
            perPage: $request->input('per_page', 50),
            filters: $request->only([
                'session_id', 'class_id', 'section_id', 'teacher_id', 
                'day_of_week', 'routine_type', 'status', 'is_published'
            ])
        );

        return RoutineResource::collection($routines);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room_id' => 'required|exists:rooms,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'period_id' => 'nullable|exists:periods,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'routine_type' => 'required|in:class,teacher,student,exam,practical,laboratory,special',
        ]);

        $routine = $this->routineService->create($request->all());

        if (!$routine) {
            return $this->error('Routine conflicts with existing schedule', 422);
        }

        return $this->created(
            new RoutineResource($routine->load(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot'])),
            'Routine created successfully'
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $routine = $this->routineService->findByUuid($uuid);

        if (!$routine) {
            return $this->notFound('Routine not found');
        }

        return $this->success(
            new RoutineResource($routine->load(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot']))
        );
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $routine = $this->routineService->findByUuid($uuid);

        if (!$routine) {
            return $this->notFound('Routine not found');
        }

        $updatedRoutine = $this->routineService->update($routine, $request->all());

        return $this->updated(
            new RoutineResource($updatedRoutine->load(['class', 'section', 'subject', 'teacher', 'room', 'timeSlot'])),
            'Routine updated successfully'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $routine = $this->routineService->findByUuid($uuid);

        if (!$routine) {
            return $this->notFound('Routine not found');
        }

        $this->routineService->delete($routine);

        return $this->deleted('Routine deleted successfully');
    }

    // ===================== BULK OPERATIONS =====================

    public function bulkCreate(Request $request): JsonResponse
    {
        $request->validate([
            'routines' => 'required|array',
            'routines.*.session_id' => 'required|exists:academic_sessions,id',
            'routines.*.class_id' => 'required|exists:classes,id',
            'routines.*.subject_id' => 'required|exists:subjects,id',
            'routines.*.teacher_id' => 'required|exists:teachers,id',
            'routines.*.room_id' => 'required|exists:rooms,id',
            'routines.*.time_slot_id' => 'required|exists:time_slots,id',
            'routines.*.day_of_week' => 'required|integer|min:0|max:6',
        ]);

        $result = $this->routineService->bulkCreate($request->input('routines'));

        return $this->success($result, 'Routines created');
    }

    // ===================== PUBLISH =====================

    public function publish(Request $request): JsonResponse
    {
        $request->validate([
            'routine_ids' => 'required|array',
            'routine_ids.*' => 'required|exists:routines,id',
        ]);

        $this->routineService->publishRoutines($request->input('routine_ids'), auth()->id());

        return $this->success(null, 'Routines published successfully');
    }

    // ===================== GENERATOR =====================

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $result = $this->routineService->autoGenerate(
            $request->input('session_id'),
            $request->input('class_id'),
            $request->input('section_id')
        );

        return $this->success($result, 'Routine generated successfully');
    }

    // ===================== CONFLICT DETECTION =====================

    public function checkConflicts(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);

        $conflicts = $this->routineService->checkConflicts(
            $request->input('teacher_id'),
            $request->input('day_of_week'),
            $request->input('time_slot_id')
        );

        return $this->success($conflicts);
    }

    // ===================== TEACHER ROUTINE =====================

    public function teacherRoutine(string $uuid): JsonResponse
    {
        $routine = $this->routineService->getTeacherRoutine($uuid);

        return $this->success($routine);
    }

    // ===================== STUDENT ROUTINE =====================

    public function studentRoutine(string $uuid): JsonResponse
    {
        $routine = $this->routineService->getStudentRoutine($uuid);

        return $this->success($routine);
    }

    // ===================== CLASS ROUTINE =====================

    public function classRoutine(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $routine = $this->routineService->getClassRoutine(
            $request->input('class_id'),
            $request->input('section_id')
        );

        return $this->success($routine);
    }

    // ===================== TIME SLOTS =====================

    public function getTimeSlots(): JsonResponse
    {
        $timeSlots = $this->routineService->getTimeSlots();

        return $this->success($timeSlots);
    }

    public function createTimeSlot(Request $request): JsonResponse
    {
        $timeSlot = $this->routineService->createTimeSlot($request->all());

        return $this->created($timeSlot, 'Time slot created successfully');
    }

    // ===================== ROOMS =====================

    public function getRooms(Request $request): JsonResponse
    {
        $rooms = $this->routineService->getRooms(
            $request->input('room_type'),
            $request->input('building')
        );

        return $this->success($rooms);
    }

    public function createRoom(Request $request): JsonResponse
    {
        $room = $this->routineService->createRoom($request->all());

        return $this->created($room, 'Room created successfully');
    }

    // ===================== CALENDAR =====================

    public function getCalendar(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'nullable|exists:academic_sessions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $events = $this->routineService->getCalendar(
            $request->input('session_id'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return $this->success($events);
    }

    public function createCalendarEvent(Request $request): JsonResponse
    {
        $event = $this->routineService->createCalendarEvent($request->all());

        return $this->created($event, 'Calendar event created successfully');
    }

    // ===================== HOLIDAYS =====================

    public function getHolidays(Request $request): JsonResponse
    {
        $holidays = $this->routineService->getHolidays($request->input('year'));

        return $this->success($holidays);
    }

    public function createHoliday(Request $request): JsonResponse
    {
        $holiday = $this->routineService->createHoliday($request->all());

        return $this->created($holiday, 'Holiday created successfully');
    }

    public function deleteHoliday(string $uuid): JsonResponse
    {
        $this->routineService->deleteHoliday($uuid);

        return $this->deleted('Holiday deleted successfully');
    }

    // ===================== EXPORT =====================

    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'format' => 'required|in:pdf,excel,csv,ics',
        ]);

        $url = $this->routineService->export(
            $request->input('class_id'),
            $request->input('format')
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
