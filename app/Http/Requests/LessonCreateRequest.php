<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'content'=> ['required', 'string'],
            'order'=> ['nullable', 'integer'],
        ];
    }
}
