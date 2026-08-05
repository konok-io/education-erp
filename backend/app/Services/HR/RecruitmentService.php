<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\JobCircular;
use App\Models\HR\JobApplication;
use App\Models\HR\Interview;
use App\Models\HR\OfferLetter;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecruitmentService
{
    // ===================== JOB CIRCULAR =====================

    public function getJobCirculars(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = JobCircular::with(['department', 'designation', 'employmentType']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('circular_no', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createJobCircular(array $data): JobCircular
    {
        return JobCircular::create([
            'uuid' => (string) Str::uuid(),
            'circular_no' => JobCircular::generateCircularNo(),
            'title' => $data['title'],
            'title_bn' => $data['title_bn'] ?? null,
            'description' => $data['description'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'benefits' => $data['benefits'] ?? null,
            'job_code' => $data['job_code'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'designation_id' => $data['designation_id'] ?? null,
            'employment_type_id' => $data['employment_type_id'] ?? null,
            'vacancy' => $data['vacancy'] ?? 1,
            'min_salary' => $data['min_salary'] ?? null,
            'max_salary' => $data['max_salary'] ?? null,
            'salary_range' => $data['salary_range'] ?? null,
            'application_deadline' => $data['application_deadline'] ?? null,
            'published_date' => $data['published_date'] ?? null,
            'interview_date' => $data['interview_date'] ?? null,
            'status' => $data['status'] ?? JobCircular::STATUS_DRAFT,
            'is_active' => $data['is_active'] ?? true,
            'terms_conditions' => $data['terms_conditions'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function publishJobCircular(string $uuid): JobCircular
    {
        $circular = JobCircular::where('uuid', $uuid)->firstOrFail();
        $circular->update([
            'status' => JobCircular::STATUS_PUBLISHED,
            'published_date' => now(),
        ]);
        return $circular->fresh();
    }

    public function closeJobCircular(string $uuid): JobCircular
    {
        $circular = JobCircular::where('uuid', $uuid)->firstOrFail();
        $circular->update(['status' => JobCircular::STATUS_CLOSED]);
        return $circular->fresh();
    }

    // ===================== JOB APPLICATION =====================

    public function getJobApplications(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = JobApplication::with(['jobCircular']);

        if (!empty($filters['job_circular_id'])) {
            $query->where('job_circular_id', $filters['job_circular_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('applicant_status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('mobile', 'like', "%{$filters['search']}%")
                    ->orWhere('nid', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createJobApplication(array $data): JobApplication
    {
        return JobApplication::create([
            'uuid' => (string) Str::uuid(),
            'application_no' => JobApplication::generateApplicationNo(),
            'job_circular_id' => $data['job_circular_id'],
            'full_name' => $data['full_name'],
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'religion' => $data['religion'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'nid' => $data['nid'] ?? null,
            'passport' => $data['passport'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'alternative_mobile' => $data['alternative_mobile'] ?? null,
            'present_address' => $data['present_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'photo' => $data['photo'] ?? null,
            'cv' => $data['cv'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
            'certificates' => $data['certificates'] ?? null,
            'experience_details' => $data['experience_details'] ?? null,
            'education_details' => $data['education_details'] ?? null,
            'applicant_status' => JobApplication::STATUS_APPLIED,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function updateApplicationStatus(string $uuid, string $status, ?string $reason = null): JobApplication
    {
        $application = JobApplication::where('uuid', $uuid)->firstOrFail();
        $application->update([
            'applicant_status' => $status,
            'rejection_reason' => in_array($status, [JobApplication::STATUS_REJECTED]) ? $reason : null,
        ]);
        return $application->fresh();
    }

    // ===================== INTERVIEW =====================

    public function getInterviews(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Interview::with(['jobApplication', 'jobCircular']);

        if (!empty($filters['job_circular_id'])) {
            $query->where('job_circular_id', $filters['job_circular_id']);
        }

        if (!empty($filters['job_application_id'])) {
            $query->where('job_application_id', $filters['job_application_id']);
        }

        if (!empty($filters['decision'])) {
            $query->where('decision', $filters['decision']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('interview_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('interview_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('interview_date', 'desc')->paginate($perPage);
    }

    public function scheduleInterview(array $data): Interview
    {
        return Interview::create([
            'uuid' => (string) Str::uuid(),
            'interview_no' => Interview::generateInterviewNo(),
            'job_circular_id' => $data['job_circular_id'],
            'job_application_id' => $data['job_application_id'],
            'interview_date' => $data['interview_date'],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'venue' => $data['venue'] ?? null,
            'interview_type' => $data['interview_type'] ?? Interview::TYPE_PERSONAL,
            'panel_members' => $data['panel_members'] ?? null,
            'total_marks' => $data['total_marks'] ?? 100,
            'decision' => Interview::DECISION_PENDING,
        ]);
    }

    public function evaluateCandidate(string $uuid, array $data): Interview
    {
        $interview = Interview::where('uuid', $uuid)->firstOrFail();

        $interview->update([
            'obtained_marks' => $data['obtained_marks'] ?? null,
            'questions' => $data['questions'] ?? null,
            'answers' => $data['answers'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'evaluation_scores' => $data['evaluation_scores'] ?? null,
            'decision' => $data['decision'] ?? Interview::DECISION_PENDING,
            'rating' => $data['rating'] ?? null,
        ]);

        // Update application status based on interview decision
        if (!empty($data['decision'])) {
            $statusMap = [
                Interview::DECISION_SELECTED => JobApplication::STATUS_SELECTED,
                Interview::DECISION_REJECTED => JobApplication::STATUS_REJECTED,
                Interview::DECISION_WAITING_LIST => JobApplication::STATUS_WAITING_LIST,
            ];

            if (isset($statusMap[$data['decision']])) {
                $interview->jobApplication->update([
                    'applicant_status' => $statusMap[$data['decision']],
                ]);
            }
        }

        return $interview->fresh();
    }

    // ===================== OFFER LETTER =====================

    public function getOfferLetters(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = OfferLetter::with(['jobApplication', 'designation', 'department']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createOfferLetter(array $data): OfferLetter
    {
        return OfferLetter::create([
            'uuid' => (string) Str::uuid(),
            'offer_no' => OfferLetter::generateOfferNo(),
            'job_circular_id' => $data['job_circular_id'],
            'job_application_id' => $data['job_application_id'],
            'interview_id' => $data['interview_id'] ?? null,
            'candidate_name' => $data['candidate_name'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'designation_id' => $data['designation_id'],
            'department_id' => $data['department_id'] ?? null,
            'employment_type_id' => $data['employment_type_id'],
            'salary_grade_id' => $data['salary_grade_id'] ?? null,
            'offered_salary' => $data['offered_salary'] ?? null,
            'offer_date' => $data['offer_date'],
            'joining_date' => $data['joining_date'],
            'terms_conditions' => $data['terms_conditions'] ?? null,
            'benefits' => $data['benefits'] ?? null,
            'status' => OfferLetter::STATUS_DRAFT,
        ]);
    }

    public function sendOfferLetter(string $uuid): OfferLetter
    {
        $offer = OfferLetter::where('uuid', $uuid)->firstOrFail();
        $offer->update(['status' => OfferLetter::STATUS_SENT]);
        return $offer->fresh();
    }

    public function acceptOfferLetter(string $uuid): OfferLetter
    {
        $offer = OfferLetter::where('uuid', $uuid)->firstOrFail();
        $offer->update([
            'status' => OfferLetter::STATUS_ACCEPTED,
            'response_date' => now(),
        ]);
        return $offer->fresh();
    }

    public function declineOfferLetter(string $uuid, ?string $reason = null): OfferLetter
    {
        $offer = OfferLetter::where('uuid', $uuid)->firstOrFail();
        $offer->update([
            'status' => OfferLetter::STATUS_DECLINED,
            'response_date' => now(),
            'response_notes' => $reason,
        ]);
        return $offer->fresh();
    }

    public function markJoined(string $uuid): OfferLetter
    {
        $offer = OfferLetter::where('uuid', $uuid)->firstOrFail();
        $offer->update([
            'status' => OfferLetter::STATUS_JOINED,
        ]);

        // Update application status
        $offer->jobApplication->update([
            'applicant_status' => 'joined',
        ]);

        return $offer->fresh();
    }

    // ===================== STATISTICS =====================

    public function getRecruitmentStats(): array
    {
        return [
            'total_circulars' => JobCircular::count(),
            'active_circulars' => JobCircular::where('status', JobCircular::STATUS_PUBLISHED)->count(),
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::where('applicant_status', JobApplication::STATUS_APPLIED)->count(),
            'shortlisted' => JobApplication::where('applicant_status', JobApplication::STATUS_SHORTLISTED)->count(),
            'interviews_scheduled' => Interview::where('decision', Interview::DECISION_PENDING)->count(),
            'selected' => JobApplication::where('applicant_status', JobApplication::STATUS_SELECTED)->count(),
            'offers_sent' => OfferLetter::count(),
            'offers_accepted' => OfferLetter::where('status', OfferLetter::STATUS_ACCEPTED)->count(),
            'joined' => OfferLetter::where('status', OfferLetter::STATUS_JOINED)->count(),
        ];
    }
}
