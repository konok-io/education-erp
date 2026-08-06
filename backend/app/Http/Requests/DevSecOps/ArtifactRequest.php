<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class ArtifactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'version' => 'nullable|string|max:100',
            'type' => 'required|string|in:docker,npm,composer,android_apk,android_aab,electron,archive,helm',
            'path' => 'nullable|string|max:500',
            'registry' => 'required|string|in:dockerhub,ghcr,nexus,artifactory,s3',
            'repository' => 'nullable|string|max:500',
            'digest' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
            'labels' => 'nullable|array',
            'signed' => 'boolean',
            'license' => 'nullable|string|max:100',
        ];
    }
}
