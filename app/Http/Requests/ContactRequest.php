<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'required|regex:/^\\+?[0-9]{10,15}$/|unique:contacts,phone_number,' . $this->route('id'),
        ];
    }


    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'A user with this phone number already exists.',
            'phone_number.regex' => 'Please enter a valid phone number in the format: +910000000000.',
        ];
    }
}
