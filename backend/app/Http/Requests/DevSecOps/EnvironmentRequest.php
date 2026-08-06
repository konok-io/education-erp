<?php

declare(strict_types=1);

namespace App\Http\Requests\DevSecOps;

use Illuminate\Foundation\Http\FormRequest;

class EnvironmentRequest extends FormRequest
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
            'type' => 'required|string|in:development,qa,uat,staging,production',
            'cluster' => 'nullable|string|max:255',
            'namespace' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['slug'] = 'sometimes|string|max:255';
            $rules['type'] = 'sometimes|string|in:development,qa,uat,staging,production';
        }

        return $rules;
    }
}
