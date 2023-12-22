<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title' => 'required|max:255',
            'is_featured' => 'required',
            'status' => 'required',
            'excerpt' => 'required|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,svg|max:2048',
            'content' => 'required',
            'posted_at' => 'required|date',
            'category_id' => 'required',
        ];
    }
}