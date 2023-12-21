<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            'email' => 'required|email:accounts,email',
            'password' => 'required|confirmed|min:8',
        ];
    }
    public function messages()
    {
        return [
            //
            'email.email' => 'The email field must be a valid email address.',
        ];
    }
}