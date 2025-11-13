<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\LessonCreateRequest;
use App\Http\Requests\LessonUpdateRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends ApiController 
{
    /**
     * Display a listing of the lessons.
     *
     * @return AnonymousResourceCollection List of lessons as a resource collection
     */
    public function index(): AnonymousResourceCollection
    {
        return LessonResource::collection(Lesson::paginate(5));
    }

    /**
     * Store a newly created lesson in storage.
     *
     * @param LessonCreateRequest $request The validated lesson creation request
     * @return JsonResponse The created lesson resource with 201 status code
     */
    public function store(LessonCreateRequest $request): JsonResponse
    {
        $course = Course::findOrFail($request->course_id);

        $this->authorize('create', [Course::class, $course]);

        try {
            $question = Lesson::create($request->validated());

            return (new LessonResource($question))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to create lesson'], 500);

        }
    }

    /**
     * Display the specified lesson.
     *
     * @param Lesson $lesson The lesson model instance
     * @return LessonResource The lesson resource
     */
    public function show(Lesson $lesson): LessonResource
    {
        return new LessonResource($lesson);
    }

    /**
     * Update the specified lesson in storage.
     *
     * @param LessonUpdateRequest $request The validated lesson update request
     * @param Lesson $lesson The lesson model instance to update
     * @return JsonResponse The updated lesson resource
     */
    public function update(LessonUpdateRequest $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $lesson);

        try {
            $lesson->update($request->validated());
            return (new LessonResource($lesson))->response();
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to update lesson.'], 500);
        }
    }

    /**
     * Remove the specified lesson from storage.
     *
     * @param Lesson $lesson The lesson model instance to delete
     * @return JsonResponse Success message response
     */
    public function destroy(Lesson $lesson): JsonResponse
    {
        $this->authorize('delete', $lesson);

        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully.']);
    }
}
