<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CourseController extends ApiController
{
    /**
     * Display a listing of the courses.
     *
     * @return AnonymousResourceCollection List of courses as a resource collection
     */
    public function index(): AnonymousResourceCollection
    {
        return CourseResource::collection(Course::with('teacher')->withCount('lessons')->paginate(5));
    }

    /**
     * Store a newly created course in storage.
     *
     * @param CourseCreateRequest $request The validated course creation request
     * @return JsonResponse The created course resource with 201 status code
     */
    public function store(CourseCreateRequest $request): JsonResponse
    {
        $this->authorize('create', Course::class);
        
        $teacher = auth()->user();

        $data = $request->validated();
        $data['teacher_id'] = $teacher->id;

        try {
            $course = DB::transaction(function () use ($data, $teacher){
                $course = Course::create($data);

                // Add the teacher to the course_user pivot table
                $course->users()->attach($teacher->id, ['role' => UserRole::TEACHER->value]);
                return $course;
            });

            return (new CourseResource($course))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to create course'], 500);
        }
    }

    /**
     * Display the specified course with its lessons and teacher.
     *
     * @param Course $course The course model instance
     * @return CourseResource The course resource with loaded relationships
     */
    public function show(Course $course): CourseResource
    {
        $course->load(['lessons', 'teacher']);
        return new CourseResource($course);
    }

    /**
     * Update the specified course in storage.
     *
     * @param CourseUpdateRequest $request The validated course update request
     * @param Course $course The course model instance to update
     * @return JsonResponse The updated course resource
     */
    public function update(CourseUpdateRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        try {
            $course->update($request->validated());
            $course->refresh()->load(['teacher']);
            return (new CourseResource($course))->response();
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Failed to update course'], 500);
        }
    }

    /**
     * Remove the specified course from storage.
     *
     * @param Course $course The course model instance to delete
     * @return JsonResponse Success message response
     */
    public function destroy(Course $course): JsonResponse
    {
        $this->authorize('delete', $course);

        $course->delete();
        return response()->json(['message' => 'Course deleted successfully.']);
    }
}