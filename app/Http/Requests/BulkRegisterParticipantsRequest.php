<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkRegisterParticipantsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|distinct',
        ];
    }

    public function messages()
    {
        return [
            'emails.required' => 'The emails field is required.',
            'emails.array' => 'The emails field must be an array.',
            'emails.min' => 'You must provide at least one email address.',
            'emails.*.required' => 'Each email address is required.',
            'emails.*.email' => 'Each email address must be valid.',
            'emails.*.distinct' => 'Duplicate email addresses are not allowed.',
        ];
    }
}
