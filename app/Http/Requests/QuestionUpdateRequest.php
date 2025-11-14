<?php

namespace App\Http\Requests;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => ['sometimes', 'string', 'max:1000'],
            'type' => ['sometimes', Rule::enum(QuestionType::class)],
        ];
    }
}
