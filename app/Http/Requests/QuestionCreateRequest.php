<?php

namespace App\Http\Requests;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quiz_id' => ['required', 'exists:quizzes,id'],
            'text' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::enum(QuestionType::class)],
            ];
    }
}
