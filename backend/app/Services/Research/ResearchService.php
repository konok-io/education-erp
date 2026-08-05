<?php

declare(strict_types=1);

namespace App\Services\Research;

use App\Models\Research\Citation;
use App\Models\Research\FundingAgency;
use App\Models\Research\Innovation;
use App\Models\Research\Patent;
use App\Models\Research\Publication;
use App\Models\Research\ResearchActivity;
use App\Models\Research\ResearchGrant;
use App\Models\Research\ResearchMilestone;
use App\Models\Research\ResearchProject;
use App\Models\Research\ResearchRepository;
use App\Models\Research\ResearchTeam;
use App\Models\Research\Thesis;
use Illuminate\Support\Facades\DB;

class ResearchService
{
    // ===================== PROJECTS =====================

    public function getProjects(array $filters = [])
    {
        $query = ResearchProject::with(['principalInvestigator', 'teams']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('project_title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('project_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['research_type'])) {
            $query->where('research_type', $filters['research_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getProject(string $uuid): ResearchProject
    {
        return ResearchProject::with(['principalInvestigator', 'teams', 'milestones', 'grants', 'publications'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function createProject(array $data): ResearchProject
    {
        return DB::transaction(function () use ($data) {
            $data['project_code'] = ResearchProject::generateProjectCode();
            $project = ResearchProject::create($data);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PROJECT_CREATED,
                'research_project',
                $project->id
            );

            return $project;
        });
    }

    public function updateProject(ResearchProject $project, array $data): ResearchProject
    {
        return DB::transaction(function () use ($project, $data) {
            $oldValues = $project->toArray();
            $project->update($data);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PROJECT_UPDATED,
                'research_project',
                $project->id,
                $oldValues,
                $project->toArray()
            );

            return $project;
        });
    }

    public function approveProject(ResearchProject $project, int $userId): ResearchProject
    {
        return DB::transaction(function () use ($project, $userId) {
            $project->approve($userId);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PROJECT_APPROVED,
                'research_project',
                $project->id
            );

            return $project;
        });
    }

    public function completeProject(ResearchProject $project): ResearchProject
    {
        return DB::transaction(function () use ($project) {
            $project->complete();

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PROJECT_COMPLETED,
                'research_project',
                $project->id
            );

            return $project;
        });
    }

    // ===================== TEAMS =====================

    public function addTeamMember(ResearchProject $project, array $data): ResearchTeam
    {
        $data['project_id'] = $project->id;
        $team = ResearchTeam::create($data);

        ResearchActivity::log(
            ResearchActivity::ACTIVITY_TEAM_ADDED,
            'research_team',
            $team->id
        );

        return $team;
    }

    public function removeTeamMember(ResearchTeam $team): void
    {
        $team->delete();
    }

    // ===================== MILESTONES =====================

    public function createMilestone(ResearchProject $project, array $data): ResearchMilestone
    {
        $data['project_id'] = $project->id;
        $milestone = ResearchMilestone::create($data);

        ResearchActivity::log(
            ResearchActivity::ACTIVITY_MILESTONE_COMPLETED,
            'research_milestone',
            $milestone->id
        );

        return $milestone;
    }

    public function updateMilestoneProgress(ResearchMilestone $milestone, int $percentage): ResearchMilestone
    {
        $milestone->updateProgress($percentage);

        if ($percentage === 100) {
            ResearchActivity::log(
                ResearchActivity::ACTIVITY_MILESTONE_COMPLETED,
                'research_milestone',
                $milestone->id
            );
        }

        return $milestone;
    }

    // ===================== GRANTS =====================

    public function getGrants(array $filters = [])
    {
        $query = ResearchGrant::with(['project', 'fundingAgency']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createGrant(array $data): ResearchGrant
    {
        $data['grant_number'] = ResearchGrant::generateGrantNumber();
        return ResearchGrant::create($data);
    }

    public function approveGrant(ResearchGrant $grant): ResearchGrant
    {
        $grant->update(['status' => ResearchGrant::STATUS_APPROVED]);

        ResearchActivity::log(
            ResearchActivity::ACTIVITY_GRANT_APPROVED,
            'research_grant',
            $grant->id
        );

        return $grant;
    }

    public function releaseGrantAmount(ResearchGrant $grant, float $amount): ResearchGrant
    {
        $grant->releaseAmount($amount);
        return $grant;
    }

    // ===================== FUNDING AGENCIES =====================

    public function getFundingAgencies(array $filters = [])
    {
        $query = FundingAgency::query();

        if (!empty($filters['agency_type'])) {
            $query->where('agency_type', $filters['agency_type']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('agency_name')->get();
    }

    public function createFundingAgency(array $data): FundingAgency
    {
        $data['agency_code'] = FundingAgency::generateAgencyCode();
        return FundingAgency::create($data);
    }

    // ===================== PUBLICATIONS =====================

    public function getPublications(array $filters = [])
    {
        $query = Publication::with(['project']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('doi', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['publication_type'])) {
            $query->where('publication_type', $filters['publication_type']);
        }

        if (!empty($filters['publication_year'])) {
            $query->where('publication_year', $filters['publication_year']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createPublication(array $data): Publication
    {
        return DB::transaction(function () use ($data) {
            $data['publication_code'] = Publication::generatePublicationCode();
            $publication = Publication::create($data);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PUBLICATION_ADDED,
                'publication',
                $publication->id
            );

            return $publication;
        });
    }

    public function updatePublication(Publication $publication, array $data): Publication
    {
        $publication->update($data);
        return $publication;
    }

    // ===================== PATENTS =====================

    public function getPatents(array $filters = [])
    {
        $query = Patent::with(['project']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createPatent(array $data): Patent
    {
        return DB::transaction(function () use ($data) {
            $data['patent_number'] = Patent::generatePatentNumber();
            $patent = Patent::create($data);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_PATENT_REGISTERED,
                'patent',
                $patent->id
            );

            return $patent;
        });
    }

    // ===================== THESES =====================

    public function getTheses(array $filters = [])
    {
        $query = Thesis::query();

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createThesis(array $data): Thesis
    {
        $data['thesis_number'] = Thesis::generateThesisNumber();
        return Thesis::create($data);
    }

    // ===================== INNOVATIONS =====================

    public function getInnovations(array $filters = [])
    {
        $query = Innovation::with(['project']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function createInnovation(array $data): Innovation
    {
        $data['innovation_code'] = Innovation::generateInnovationCode();
        return Innovation::create($data);
    }

    // ===================== REPOSITORY =====================

    public function getRepository(array $filters = [])
    {
        $query = ResearchRepository::with(['project', 'publication']);

        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (!empty($filters['access_type'])) {
            $query->where('access_type', $filters['access_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function uploadToRepository(array $data): ResearchRepository
    {
        return DB::transaction(function () use ($data) {
            $data['document_code'] = ResearchRepository::generateDocumentCode();
            $doc = ResearchRepository::create($data);

            ResearchActivity::log(
                ResearchActivity::ACTIVITY_REPOSITORY_UPDATED,
                'research_repository',
                $doc->id
            );

            return $doc;
        });
    }

    // ===================== CITATIONS =====================

    public function addCitation(Publication $publication, array $data): Citation
    {
        $data['publication_id'] = $publication->id;
        $citation = Citation::create($data);
        $publication->incrementCitations();
        return $citation;
    }

    // ===================== DASHBOARD =====================

    public function getDashboardData(): array
    {
        return [
            'total_projects' => ResearchProject::count(),
            'active_projects' => ResearchProject::where('status', ResearchProject::STATUS_ACTIVE)->count(),
            'completed_projects' => ResearchProject::where('status', ResearchProject::STATUS_COMPLETED)->count(),
            'total_grants' => ResearchGrant::count(),
            'active_grants' => ResearchGrant::where('status', ResearchGrant::STATUS_ACTIVE)->count(),
            'total_funding' => ResearchGrant::where('status', ResearchGrant::STATUS_ACTIVE)->sum('grant_amount'),
            'total_publications' => Publication::count(),
            'published_publications' => Publication::where('status', Publication::STATUS_PUBLISHED)->count(),
            'total_citations' => Publication::sum('citation_count'),
            'total_patents' => Patent::count(),
            'granted_patents' => Patent::where('status', Patent::STATUS_GRANTED)->count(),
            'total_theses' => Thesis::count(),
            'total_innovations' => Innovation::count(),
            'repository_items' => ResearchRepository::count(),
            'research_students' => ResearchTeam::where('role', ResearchTeam::ROLE_STUDENT)->count(),
        ];
    }
}
