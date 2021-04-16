<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'content' => 'string|nullable',
            'attachment' => 'file|nullable|max:5000',
            'is_seen' => 'boolean',
            'is_sent' => 'boolean',
            'chat_id' => 'integer|nullable',
            'group_chat_id' => 'integer|nullable',
        ];
    }
}
