<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('user.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'mobile' => ['nullable', 'string', 'max:20', 'unique:users,mobile'],
            'password' => [
                'required',
                'string',
                'min:12',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed',
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'blocked', 'suspended', 'pending'])],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, one number and one special character.',
            'password.min' => 'Password must be at least 12 characters long.',
            'email.unique' => 'This email is already taken.',
            'mobile.unique' => 'This mobile number is already taken.',
        ]);
    }
}
