<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\Course;
use App\Models\File;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    // List of valid models for fileable relation
    protected $allowedTypes = [
            'course' => Course::class,
            'lesson' => Lesson::class,
            // 'quiz' => Quiz::class,
            // 'question' => Question::class,
        ];

    public function upload(FileUploadRequest $request, string $type, int $id)
    {
        $request->validated();

        // Type check
        if (!array_key_exists($type, $this->allowedTypes)) {
            return response()->json(['error' => 'Incalid fileable type.'], 422);
        }

        $modelClass = $this->allowedTypes[$type];
        $model = $modelClass::find($id);

        if (!$model) {
            return response()->json(['error' => 'Target model not found'], 404);
        }
 
        // Upload file
        $uploaded = $request->file('file');
        $filename = Str::uuid() . '.' . $uploaded->getClientOriginalExtension();
        $path = $uploaded->storeAs('uploads/' . $type, $filename, 'public' );

        // Create file record
        $file = new File([
            'name' => $uploaded->getClientOriginalName(),
            'path' => $path,
            'type' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
        ]);

        $model->files()->save($file);

        return response()->json([
            'message' => 'File uploded succesfully.',
            'file' => [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'size' => $file->size,
                'url' => $file->url,
            ],
        ], 201);
    }

    public function destroy(File $file)
    {
        Storage::disk('public')->delete($file->path);

        $file->delete();

        return response()->json(['message' => 'File deleted successfully.']);
    }
}
