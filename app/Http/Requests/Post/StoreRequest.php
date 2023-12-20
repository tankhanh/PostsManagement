<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Xác định liệu người dùng có được phép thực hiện yêu cầu hay không.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Lấy ra các quy tắc xác minh áp dụng cho yêu cầu.
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
            'image' => 'required|mimes:jpeg,png,jpg,svg|max:2048',
            'content' => 'required',
            'posted_at' => 'required|date'
        ];
    }

    /**
     * Trích xuất URL hình ảnh từ trường nội dung.
     *
     * @param  string  $content
     * @return array
     */
}