<?php

declare(strict_types=1);

namespace App\Services\Alumni;

use App\Models\Alumni\AlumniActivity;
use App\Models\Alumni\AlumniEvent;
use App\Models\Alumni\AlumniProfile;
use App\Models\Alumni\Donation;
use App\Models\Alumni\Employer;
use App\Models\Alumni\EventRegistration;
use App\Models\Alumni\FundraisingCampaign;
use App\Models\Alumni\Internship;
use App\Models\Alumni\InternshipApplication;
use App\Models\Alumni\Job;
use App\Models\Alumni\JobApplication;
use App\Models\Alumni\Mentorship;
use App\Models\Alumni\Placement;
use Illuminate\Support\Facades\DB;

class AlumniService
{
    // ===================== ALUMNI PROFILE =====================

    public function getAlumniProfiles(array $filters = [])
    {
        $query = AlumniProfile::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('membership_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['passing_year'])) {
            $query->where('passing_year', $filters['passing_year']);
        }

        if (!empty($filters['membership_type'])) {
            $query->where('membership_type', $filters['membership_type']);
        }

        if (!empty($filters['employment_status'])) {
            $query->where('employment_status', $filters['employment_status']);
        }

        if (!empty($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getAlumniProfile(string $uuid): AlumniProfile
    {
        return AlumniProfile::where('uuid', $uuid)->firstOrFail();
    }

    public function createAlumniProfile(array $data): AlumniProfile
    {
        return DB::transaction(function () use ($data) {
            $data['membership_number'] = AlumniProfile::generateMembershipNumber();
            $data['verification_token'] = AlumniProfile::generateVerificationToken();

            $profile = AlumniProfile::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_ALUMNI_REGISTERED,
                'alumni_profile',
                $profile->id
            );

            return $profile;
        });
    }

    public function updateAlumniProfile(AlumniProfile $profile, array $data): AlumniProfile
    {
        return DB::transaction(function () use ($profile, $data) {
            $oldValues = $profile->toArray();
            $profile->update($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_PROFILE_UPDATED,
                'alumni_profile',
                $profile->id,
                $oldValues,
                $profile->toArray()
            );

            return $profile;
        });
    }

    public function verifyAlumniProfile(AlumniProfile $profile, int $userId): AlumniProfile
    {
        return DB::transaction(function () use ($profile, $userId) {
            $profile->verify($userId);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_ALUMNI_VERIFIED,
                'alumni_profile',
                $profile->id
            );

            return $profile;
        });
    }

    // ===================== EMPLOYER =====================

    public function getEmployers(array $filters = [])
    {
        $query = Employer::query();

        if (!empty($filters['search'])) {
            $query->where('company_name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['industry'])) {
            $query->where('industry', $filters['industry']);
        }

        if (!empty($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createEmployer(array $data): Employer
    {
        $data['company_code'] = Employer::generateCompanyCode();
        return Employer::create($data);
    }

    public function verifyEmployer(Employer $employer, int $userId): Employer
    {
        $employer->verify($userId);
        return $employer;
    }

    // ===================== JOBS =====================

    public function getJobs(array $filters = [])
    {
        $query = Job::with('employer');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('job_title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('job_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['job_type'])) {
            $query->where('job_type', $filters['job_type']);
        }

        if (!empty($filters['work_type'])) {
            $query->where('work_type', $filters['work_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', Job::STATUS_OPEN);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createJob(array $data): Job
    {
        return DB::transaction(function () use ($data) {
            $data['job_number'] = Job::generateJobNumber();
            $job = Job::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_JOB_POSTED,
                'job',
                $job->id
            );

            return $job;
        });
    }

    public function publishJob(Job $job): Job
    {
        $job->publish();
        return $job;
    }

    // ===================== JOB APPLICATIONS =====================

    public function applyForJob(Job $job, array $data): JobApplication
    {
        return DB::transaction(function () use ($job, $data) {
            $data['job_id'] = $job->id;
            $application = JobApplication::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_APPLICATION_SUBMITTED,
                'job_application',
                $application->id
            );

            return $application;
        });
    }

    public function updateApplicationStatus(JobApplication $application, string $status): JobApplication
    {
        $application->update(['status' => $status]);
        return $application;
    }

    // ===================== INTERNSHIPS =====================

    public function getInternships(array $filters = [])
    {
        $query = Internship::with('employer');

        if (!empty($filters['search'])) {
            $query->where('internship_title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['internship_type'])) {
            $query->where('internship_type', $filters['internship_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', Internship::STATUS_OPEN);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createInternship(array $data): Internship
    {
        $data['internship_number'] = Internship::generateInternshipNumber();
        return Internship::create($data);
    }

    public function applyForInternship(Internship $internship, array $data): InternshipApplication
    {
        $data['internship_id'] = $internship->id;
        return InternshipApplication::create($data);
    }

    // ===================== PLACEMENTS =====================

    public function getPlacements(array $filters = [])
    {
        $query = Placement::with(['employer', 'alumniProfile']);

        if (!empty($filters['employer_id'])) {
            $query->where('employer_id', $filters['employer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('joining_date', $filters['year']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createPlacement(array $data): Placement
    {
        return DB::transaction(function () use ($data) {
            $data['placement_number'] = Placement::generatePlacementNumber();
            $placement = Placement::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_PLACEMENT_COMPLETED,
                'placement',
                $placement->id
            );

            return $placement;
        });
    }

    // ===================== EVENTS =====================

    public function getEvents(array $filters = [])
    {
        $query = AlumniEvent::query();

        if (!empty($filters['search'])) {
            $query->where('event_title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('event_date', 'desc')->paginate(20);
    }

    public function createEvent(array $data): AlumniEvent
    {
        return DB::transaction(function () use ($data) {
            $data['event_number'] = AlumniEvent::generateEventNumber();
            $event = AlumniEvent::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_EVENT_CREATED,
                'alumni_event',
                $event->id
            );

            return $event;
        });
    }

    public function registerForEvent(AlumniEvent $event, array $data): EventRegistration
    {
        return DB::transaction(function () use ($event, $data) {
            $data['event_id'] = $event->id;
            $registration = EventRegistration::create($data);

            $event->increment('registered_count');

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_REGISTRATION_CREATED,
                'event_registration',
                $registration->id
            );

            return $registration;
        });
    }

    // ===================== MENTORSHIP =====================

    public function getMentorships(array $filters = [])
    {
        $query = Mentorship::with(['mentor', 'mentee']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createMentorship(array $data): Mentorship
    {
        return DB::transaction(function () use ($data) {
            $data['mentorship_number'] = Mentorship::generateMentorshipNumber();
            $mentorship = Mentorship::create($data);

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_MENTORSHIP_STARTED,
                'mentorship',
                $mentorship->id
            );

            return $mentorship;
        });
    }

    // ===================== DONATIONS =====================

    public function getDonations(array $filters = [])
    {
        $query = Donation::with(['campaign', 'alumniProfile']);

        if (!empty($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createDonation(array $data): Donation
    {
        return DB::transaction(function () use ($data) {
            $data['donation_number'] = Donation::generateDonationNumber();
            $donation = Donation::create($data);

            if ($donation->campaign_id) {
                $campaign = FundraisingCampaign::find($donation->campaign_id);
                $campaign->updateRaisedAmount();
            }

            AlumniActivity::log(
                AlumniActivity::ACTIVITY_DONATION_RECEIVED,
                'donation',
                $donation->id
            );

            return $donation;
        });
    }

    // ===================== CAMPAIGNS =====================

    public function getCampaigns(array $filters = [])
    {
        $query = FundraisingCampaign::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createCampaign(array $data): FundraisingCampaign
    {
        $data['campaign_code'] = FundraisingCampaign::generateCampaignCode();
        return FundraisingCampaign::create($data);
    }

    // ===================== DASHBOARD =====================

    public function getDashboardData(): array
    {
        return [
            'total_alumni' => AlumniProfile::count(),
            'verified_alumni' => AlumniProfile::where('is_verified', true)->count(),
            'active_members' => AlumniProfile::where('status', AlumniProfile::STATUS_ACTIVE)->count(),
            'total_employers' => Employer::count(),
            'verified_employers' => Employer::where('is_verified', true)->count(),
            'total_jobs' => Job::count(),
            'open_jobs' => Job::where('status', Job::STATUS_OPEN)->count(),
            'total_internships' => Internship::count(),
            'open_internships' => Internship::where('status', Internship::STATUS_OPEN)->count(),
            'total_placements' => Placement::count(),
            'total_events' => AlumniEvent::count(),
            'upcoming_events' => AlumniEvent::where('event_date', '>=', now()->toDateString())->count(),
            'total_donations' => Donation::where('payment_status', Donation::PAYMENT_COMPLETED)->sum('amount'),
            'total_campaigns' => FundraisingCampaign::count(),
            'active_campaigns' => FundraisingCampaign::where('status', FundraisingCampaign::STATUS_ACTIVE)->count(),
        ];
    }
}
