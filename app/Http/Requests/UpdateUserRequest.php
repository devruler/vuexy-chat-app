<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'about' => [
                'string',
                'nullable',
            ],
            'photo' => [
                'sometimes',
                'file',
                'image',
                'max: 2000'
            ],
            'status' => [
                'string',
                'nullable',
                Rule::in(['online', 'do not disturb', 'offline', 'away']),
            ]
        ];
    }
}
