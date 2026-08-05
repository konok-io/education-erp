<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Academic;

use App\Http\Controllers\BaseController;
use App\Models\Academic\AcademicLevel;
use App\Models\Academic\Faculty;
use App\Models\Academic\Department;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Semester;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\Section;
use App\Models\Academic\Group;
use App\Models\Academic\Subject;
use App\Models\Academic\SubjectCategory;
use App\Models\Academic\ProgramSubject;
use App\Models\Academic\GradeRule;
use App\Models\Academic\GpaRule;
use App\Models\Academic\AcademicCalendar;
use App\Services\Academic\AcademicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AcademicController extends BaseController
{
    public function __construct(
        private readonly AcademicService $academicService
    ) {}

    // ===================== ACADEMIC LEVELS =====================

    public function indexAcademicLevels(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new AcademicLevel(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeAcademicLevel(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new AcademicLevel(), $request->all());
        return $this->created($data, 'Academic level created successfully');
    }

    public function showAcademicLevel(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new AcademicLevel(), $uuid);
        return $this->success($data);
    }

    public function updateAcademicLevel(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicLevel(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Academic level updated successfully');
    }

    public function destroyAcademicLevel(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicLevel(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Academic level deleted successfully');
    }

    // ===================== FACULTIES =====================

    public function indexFaculties(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Faculty(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeFaculty(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Faculty(), $request->all());
        return $this->created($data, 'Faculty created successfully');
    }

    public function showFaculty(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Faculty(), $uuid);
        return $this->success($data);
    }

    public function updateFaculty(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Faculty(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Faculty updated successfully');
    }

    public function destroyFaculty(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Faculty(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Faculty deleted successfully');
    }

    // ===================== DEPARTMENTS =====================

    public function indexDepartments(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Department(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Department(), $request->all());
        return $this->created($data, 'Department created successfully');
    }

    public function showDepartment(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Department(), $uuid);
        return $this->success($data);
    }

    public function updateDepartment(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Department(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Department updated successfully');
    }

    public function destroyDepartment(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Department(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Department deleted successfully');
    }

    // ===================== PROGRAMS =====================

    public function indexPrograms(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Program(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeProgram(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Program(), $request->all());
        return $this->created($data, 'Program created successfully');
    }

    public function showProgram(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Program(), $uuid);
        return $this->success($data);
    }

    public function updateProgram(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Program(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Program updated successfully');
    }

    public function destroyProgram(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Program(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Program deleted successfully');
    }

    // ===================== ACADEMIC SESSIONS =====================

    public function indexSessions(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new AcademicSession(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeSession(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new AcademicSession(), $request->all());
        return $this->created($data, 'Session created successfully');
    }

    public function showSession(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new AcademicSession(), $uuid);
        return $this->success($data);
    }

    public function updateSession(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicSession(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Session updated successfully');
    }

    public function destroySession(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicSession(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Session deleted successfully');
    }

    public function setCurrentSession(string $uuid): JsonResponse
    {
        $this->academicService->setCurrentSession($uuid);
        return $this->success(null, 'Current session set successfully');
    }

    // ===================== SEMESTERS =====================

    public function indexSemesters(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Semester(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeSemester(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Semester(), $request->all());
        return $this->created($data, 'Semester created successfully');
    }

    public function showSemester(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Semester(), $uuid);
        return $this->success($data);
    }

    public function updateSemester(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Semester(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Semester updated successfully');
    }

    public function destroySemester(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Semester(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Semester deleted successfully');
    }

    // ===================== CLASSES =====================

    public function indexClasses(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new AcademicClass(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeClass(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new AcademicClass(), $request->all());
        return $this->created($data, 'Class created successfully');
    }

    public function showClass(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new AcademicClass(), $uuid);
        return $this->success($data);
    }

    public function updateClass(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicClass(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Class updated successfully');
    }

    public function destroyClass(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicClass(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Class deleted successfully');
    }

    // ===================== SECTIONS =====================

    public function indexSections(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Section(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeSection(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Section(), $request->all());
        return $this->created($data, 'Section created successfully');
    }

    public function showSection(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Section(), $uuid);
        return $this->success($data);
    }

    public function updateSection(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Section(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Section updated successfully');
    }

    public function destroySection(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Section(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Section deleted successfully');
    }

    // ===================== GROUPS =====================

    public function indexGroups(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Group(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeGroup(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Group(), $request->all());
        return $this->created($data, 'Group created successfully');
    }

    public function showGroup(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Group(), $uuid);
        return $this->success($data);
    }

    public function updateGroup(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Group(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Group updated successfully');
    }

    public function destroyGroup(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Group(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Group deleted successfully');
    }

    // ===================== SUBJECT CATEGORIES =====================

    public function indexSubjectCategories(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new SubjectCategory(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeSubjectCategory(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new SubjectCategory(), $request->all());
        return $this->created($data, 'Subject category created successfully');
    }

    public function showSubjectCategory(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new SubjectCategory(), $uuid);
        return $this->success($data);
    }

    public function updateSubjectCategory(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new SubjectCategory(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Subject category updated successfully');
    }

    public function destroySubjectCategory(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new SubjectCategory(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Subject category deleted successfully');
    }

    // ===================== SUBJECTS =====================

    public function indexSubjects(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new Subject(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeSubject(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new Subject(), $request->all());
        return $this->created($data, 'Subject created successfully');
    }

    public function showSubject(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new Subject(), $uuid);
        return $this->success($data);
    }

    public function updateSubject(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Subject(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Subject updated successfully');
    }

    public function destroySubject(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new Subject(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Subject deleted successfully');
    }

    // ===================== GRADE RULES =====================

    public function indexGradeRules(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new GradeRule(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeGradeRule(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new GradeRule(), $request->all());
        return $this->created($data, 'Grade rule created successfully');
    }

    public function showGradeRule(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new GradeRule(), $uuid);
        return $this->success($data);
    }

    public function updateGradeRule(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new GradeRule(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Grade rule updated successfully');
    }

    public function destroyGradeRule(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new GradeRule(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Grade rule deleted successfully');
    }

    // ===================== GPA RULES =====================

    public function indexGpaRules(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new GpaRule(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeGpaRule(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new GpaRule(), $request->all());
        return $this->created($data, 'GPA rule created successfully');
    }

    public function showGpaRule(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new GpaRule(), $uuid);
        return $this->success($data);
    }

    public function updateGpaRule(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new GpaRule(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'GPA rule updated successfully');
    }

    public function destroyGpaRule(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new GpaRule(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('GPA rule deleted successfully');
    }

    // ===================== ACADEMIC CALENDAR =====================

    public function indexCalendar(Request $request): AnonymousResourceCollection
    {
        $data = $this->academicService->getAll(new AcademicCalendar(), $request->all());
        return $this->resourceCollection($data);
    }

    public function storeCalendar(Request $request): JsonResponse
    {
        $data = $this->academicService->create(new AcademicCalendar(), $request->all());
        return $this->created($data, 'Calendar event created successfully');
    }

    public function showCalendar(string $uuid): JsonResponse
    {
        $data = $this->academicService->findByUuid(new AcademicCalendar(), $uuid);
        return $this->success($data);
    }

    public function updateCalendar(Request $request, string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicCalendar(), $uuid);
        $data = $this->academicService->update($item, $request->all());
        return $this->updated($data, 'Calendar event updated successfully');
    }

    public function destroyCalendar(string $uuid): JsonResponse
    {
        $item = $this->academicService->findByUuid(new AcademicCalendar(), $uuid);
        $this->academicService->delete($item);
        return $this->deleted('Calendar event deleted successfully');
    }

    // ===================== LOOKUPS =====================

    public function getAcademicHierarchy(Request $request): JsonResponse
    {
        $hierarchy = $this->academicService->getAcademicHierarchy();
        return $this->success($hierarchy);
    }

    public function getSubjectsByProgram(Request $request, string $programId): JsonResponse
    {
        $subjects = $this->academicService->getSubjectsByProgram($programId);
        return $this->success($subjects);
    }

    public function getClassesBySession(Request $request, string $sessionId): JsonResponse
    {
        $classes = $this->academicService->getClassesBySession($sessionId);
        return $this->success($classes);
    }
}
