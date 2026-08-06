<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class DeploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'environment_id' => 'required|uuid|exists:devsecops_environments,id',
            'strategy' => 'required|string|in:rolling,blue_green,canary,ab,shadow,recreate',
            'pipeline_run_id' => 'nullable|uuid|exists:devsecops_pipeline_runs,id',
            'release_id' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'namespace' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'replicas' => 'nullable|array',
            'resources' => 'nullable|array',
            'health_checks' => 'nullable|array',
            'rollback_config' => 'nullable|array',
            'previous_version' => 'nullable|string|max:255',
            'commit_sha' => 'nullable|string|max:40',
            'auto_rollback' => 'boolean',
        ];
    }
}
