<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message'       => 'string|max:5000|required_without:attachment',
            'group_id'      => 'required_without:receiver_id|exists:groups,id',
            'receiver_id'   => 'required_without:group_id|exists:users,id',
            'attachments'   => 'array|max:10|required_without:message',
            'attachments.*' => 'file|max:102400',
        ];
    }
}
