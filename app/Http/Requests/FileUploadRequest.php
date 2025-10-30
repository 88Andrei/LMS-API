<?php

namespace App\Http\Requests;

use App\Enums\FileType;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf, png, jpg, jpeg, mp4'],
            'type' => ['required', 'in:' . implode(',', FileType::values())],
        ];
    }
}
