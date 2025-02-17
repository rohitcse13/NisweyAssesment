<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'xml_file' => [
                'required',
                'file',
                'mimes:xml',
                'max:5120'
            ],
        ];
    }


    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'xml_file.required' => 'Please Select XML File.',
            'xml_file.file' => 'The Uploaded Item Must Be A Valid File.',
            'xml_file.mimes' => 'Only XML Files Are Allowed.',
            'xml_file.max' => 'The XML File Must Not Exceed 5MB In Size.',
        ];
    }
}
