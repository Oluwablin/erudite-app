<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'max_participants' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The event name is required.',
            'name.string' => 'The event name must be a valid string.',
            'name.max' => 'The event name must not exceed 255 characters.',
            'start_time.required' => 'The start time is required.',
            'start_time.date' => 'The start time must be a valid date.',
            'start_time.after' => 'The start time must be in the future.',
            'end_time.required' => 'The end time is required.',
            'end_time.date' => 'The end time must be a valid date.',
            'end_time.after' => 'The end time must be after the start time.',
            'max_participants.required' => 'The maximum number of participants is required.',
            'max_participants.integer' => 'The maximum number of participants must be a number.',
            'max_participants.min' => 'The maximum number of participants must be at least 1.',
        ];
    }
}
