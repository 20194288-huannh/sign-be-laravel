<?php

namespace App\Http\Requests;

class SendSignRequest extends BaseFormRequest
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
            'users' => 'required|array',
            'users.*.name' => 'required|email',
            'users.*.email' => 'required|email',
            'users.*.type' => 'required|integer',

            'signatures' => 'required|array',
            'signatures.*.type' => 'required|integer',
            'signatures.*.scale' => 'required|numeric',
            'signatures.*.page' => 'required|integer',
            'signatures.*.can_resize' => 'required|boolean',
            'signatures.*.position' => 'required|array',
            'signatures.*.position.width' => 'required|numeric',
            'signatures.*.position.height' => 'required|numeric',
            'signatures.*.position.top' => 'required|numeric',
            'signatures.*.position.left' => 'required|numeric',
            'signatures.*.receiver' => 'required|array',
            'signatures.*.receiver.name' => 'nullable|string',
            'signatures.*.receiver.email' => 'nullable|email',
            'signatures.*.receiver.type' => 'nullable|integer',
            'signatures.*.receiverId' => 'nullable|integer',

            'email' => 'required|array',
            'email.expired_date' => 'required|date|date_format:Y-m-d|after:today',
            'email.subject' => 'required|string',
            'email.content' => 'required|string',

            'canvas' => 'required|array',
            'canvas.height' => 'required|numeric',
            'canvas.width' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'users' => 'users array',
            'users.*.name' => 'user name',
            'users.*.email' => 'user email',
            'users.*.type' => 'user type',

            'signatures' => 'signatures array',
            'signatures.*.type' => 'signature type',
            'signatures.*.scale' => 'signature scale',
            'signatures.*.page' => 'signature page',
            'signatures.*.can_resize' => 'signature can resize',
            'signatures.*.position' => 'signature position',
            'signatures.*.position.width' => 'signature position width',
            'signatures.*.position.height' => 'signature position height',
            'signatures.*.position.top' => 'signature position top',
            'signatures.*.position.left' => 'signature position left',
            'signatures.*.receiver' => 'signature receiver',
            'signatures.*.receiver.name' => 'signature receiver name',
            'signatures.*.receiver.email' => 'signature receiver email',
            'signatures.*.receiver.type' => 'signature receiver type',
            'signatures.*.receiverId' => 'signature receiver ID',

            'email' => 'email array',
            'email.expired_date' => 'expired date',
            'email.subject' => 'email subject',
            'email.content' => 'email content',

            'canvas' => 'canvas array',
            'canvas.height' => 'canvas height',
            'canvas.width' => 'canvas width',
        ];
    }
}
