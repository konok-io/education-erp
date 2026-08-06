<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class SecurityScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:sast,dast,sca,secret,container,iac,sbom,license',
            'tool' => 'required|string|max:100',
            'pipeline_run_id' => 'nullable|uuid|exists:devsecops_pipeline_runs,id',
            'artifact_id' => 'nullable|uuid|exists:devsecops_artifacts,id',
            'results' => 'nullable|array',
            'vulnerabilities' => 'nullable|array',
            'secrets_found' => 'nullable|array',
            'compliance' => 'nullable|array',
            'report_path' => 'nullable|string|max:500',
            'summary' => 'nullable|string',
        ];
    }
}
