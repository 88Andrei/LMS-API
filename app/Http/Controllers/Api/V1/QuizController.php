<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\QuizCreateRequest;
use App\Http\Requests\QuizUpdateRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class QuizController extends ApiController
{
    /**
     * Display a listing of the quizzes.
     *
     * @param Request $request The request instance
     * @return AnonymousResourceCollection List of quizzes as a resource collection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $quizzes = Quiz::query()
            ->when($request->lesson_id, fn($query) => $query->where('lesson_id', $request->lesson_id))
            ->paginate();

        return QuizResource::collection($quizzes);
    }

    /**
     * Store a newly created quiz in storage.
     *
     * @param QuizCreateRequest $request The validated quiz creation request
     * @return JsonResponse The created quiz resource with 201 status code
     */
    public function store(QuizCreateRequest $request): JsonResponse
    {
        try {
            $quiz = Quiz::create($request->validated());
            return (new QuizResource($quiz))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create quiz'], 500);
        }
    }

    /**
     * Display the specified quiz with its questions and answers.
     *
     * @param Quiz $quiz The quiz model instance (automatically resolved by route model binding)
     * @return QuizResource The quiz resource with loaded relationships
     */
    public function show(Quiz $quiz): QuizResource
    {
        return Cache::remember(
            "quiz.{$quiz->id}", 
            now()->addHour(), 
            function () use ($quiz) {
                $quiz->load('questions.answers');
                return new QuizResource($quiz);
            }
        );
    }

    /**
     * Update the specified quiz in storage.
     *
     * @param QuizUpdateRequest $request The validated quiz update request
     * @param Quiz $quiz The quiz model instance to update
     * @return QuizResource The updated quiz resource
     */
    public function update(QuizUpdateRequest $request, Quiz $quiz): JsonResponse
    {
        try {
            $quiz->update($request->validated());
            return response()->json(new QuizResource($quiz), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update quiz'], 500);
        }
    }

    /**
     * Remove the specified quiz from storage.
     *
     * @param Quiz $quiz The quiz model instance to delete
     * @return JsonResponse Success message response
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted successfully.'], 200);
    }
}
