<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('user.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userUuid = $this->route('uuid');

        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($userUuid) {
                    return $query->where('uuid', '!=', $userUuid);
                }),
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($userUuid) {
                    return $query->where('uuid', '!=', $userUuid);
                }),
            ],
            'password' => [
                'nullable',
                'string',
                'min:12',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed',
            ],
            'role_id' => ['sometimes', 'exists:roles,id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'blocked', 'suspended', 'pending'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, one number and one special character.',
        ]);
    }
}
