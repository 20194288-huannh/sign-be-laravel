<?php

namespace App\Http\Requests;

class CreateSignRequest extends BaseFormRequest
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
            'signatures' => 'required|array',
            'signatures.*.page' => 'required|numeric',
            'token' => 'required|string',
            'signatures.*.data' => 'required|array',
        ];
    }

    
    public function messages()
    {
        return [
            'signatures.*.data' => 'Need Your Signature!'
        ];
    }
}
