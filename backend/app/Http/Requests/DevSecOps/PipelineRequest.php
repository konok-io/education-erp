<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class PipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:ci,cd,security,release',
            'provider' => 'required|string|in:github,gitlab,jenkins,azure',
            'repository' => 'nullable|string|max:500',
            'branch' => 'string|max:255',
            'yaml_path' => 'string|max:500',
            'stages' => 'nullable|array',
            'config' => 'nullable|array',
            'status' => 'string|in:inactive,active,paused,archived',
            'timeout' => 'integer|min:60|max:86400',
            'auto_trigger' => 'boolean',
            'require_approval' => 'boolean',
            'approval_roles' => 'nullable|array',
            'min_coverage' => 'integer|min:0|max:100',
            'is_active' => 'boolean',
            'environment_id' => 'nullable|uuid|exists:devsecops_environments,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['slug'] = 'sometimes|string|max:255';
            $rules['type'] = 'sometimes|string|in:ci,cd,security,release';
            $rules['provider'] = 'sometimes|string|in:github,gitlab,jenkins,azure';
        }

        return $rules;
    }
}
