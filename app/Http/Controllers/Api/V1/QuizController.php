<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuizCreateRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuizController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuizResource::collection(Quiz::all());
    }

    public function store(QuizCreateRequest $request): QuizResource
    {
        $quiz = Quiz::create($request->only(['lesson_id', 'title', 'description']));

        return new QuizResource($quiz);
    }

    public function show(Quiz $quiz): QuizResource
    {
        return new QuizResource($quiz);
    }

    public function update(Request $request, Quiz $quiz): QuizResource
    {
        $quiz->update($request->only(['title', 'description']));
        return new QuizResource($quiz);
    }

    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted successfully.']);
    }
}
