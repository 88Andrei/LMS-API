<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\QuestionCreateRequest;
use App\Http\Requests\QuestionUpdateRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends ApiController
{
    /**
     * Display a listing of the questions.
     *
     * @return AnonymousResourceCollection List of questions as a resource collection
     */
    public function index(): AnonymousResourceCollection
    {
        return QuestionResource::collection(Question::with('ansvers')->paginate(5));
    }

    /**
     * Store a newly created question in srorage.
     *
     * @param QuestionCreateRequest $request The validated question creation request
     * @return JsonResponse The created question resource with 201 status code
     */
    public function store(QuestionCreateRequest $request): JsonResponse
    {
        try {
            $question = Question::create($request->validated());
            return (new QuestionResource($question))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create question'], 500);
        }
    }

    /**
     * Display the specified question with its answers.
     *
     * @param Question $question The question model instance (automatically resolved by route model binding)
     * @return QuestionResource The question resource with loaded relationships
     */
    public function show(Question $question): QuestionResource
    {
        $question->load('answers');
        return new QuestionResource($question);
    }

    /**
     * Update the specified question in storage.
     *
     * @param QuestionUpdateRequest $request The validated question update request
     * @param Question $question The question model instance to update
     * @return QuestionResource The updated question resource
     */
    public function update(QuestionUpdateRequest $request, Question $question): JsonResponse
    {
        try {
            $question->update($request->validated());
            return response()->json(new QuestionResource($question));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create question'], 500);
        }
    }

    /**
     * Remove the specified question from storage.
     *
     * @param Question $question The question model instance to delete
     * @return JsonResponse Success message response
     */
    public function destroy(Question $question): JsonResponse
    {
        $question->delete();
        return response()->json(['message' => 'Question deleted successfully.']);
    }
}
