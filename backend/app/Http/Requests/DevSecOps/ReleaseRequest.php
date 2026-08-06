<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:100',
            'type' => 'required|string|in:major,minor,patch,rc,lts,hotfix',
            'description' => 'nullable|string',
            'status' => 'string|in:draft,rc,stable,lts,deprecated,archived',
            'channel' => 'string|in:stable,beta,alpha,edge',
            'git_tag' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:40',
            'changelog' => 'nullable|array',
            'breaking_changes' => 'nullable|array',
            'known_issues' => 'nullable|array',
            'upgrade_guide' => 'nullable|array',
            'artifacts' => 'nullable|array',
            'metadata' => 'nullable|array',
            'released_at' => 'nullable|date',
            'eol_at' => 'nullable|date|after:released_at',
            'is_prerelease' => 'boolean',
            'is_draft' => 'boolean',
            'environment_id' => 'nullable|uuid|exists:devsecops_environments,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['version'] = 'sometimes|string|max:100';
            $rules['type'] = 'sometimes|string|in:major,minor,patch,rc,lts,hotfix';
        }

        return $rules;
    }
}
