<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class GitopsConfigRequest extends FormRequest
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
            'provider' => 'required|string|in:argocd,fluxcd',
            'repository' => 'required|string|max:500',
            'path' => 'required|string|max:500',
            'target_branch' => 'string|max:255',
            'environment_id' => 'required|uuid|exists:devsecops_environments,id',
            'sync_policy' => 'string|in:automated,manual',
            'auto_sync' => 'boolean',
            'self_heal' => 'boolean',
            'prune' => 'boolean',
            'sync_interval' => 'integer|min:60|max:3600',
            'kustomize' => 'nullable|array',
            'helm' => 'nullable|array',
            'values' => 'nullable|array',
            'health_check_path' => 'nullable|string|max:500',
            'drift_detection' => 'nullable|array',
            'notifications' => 'nullable|array',
            'is_active' => 'boolean',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['slug'] = 'sometimes|string|max:255';
            $rules['provider'] = 'sometimes|string|in:argocd,fluxcd';
        }

        return $rules;
    }
}
