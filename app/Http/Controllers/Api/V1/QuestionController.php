<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuestionResource::collection(Question::all());
    }

    public function store(Request $request): QuestionResource
    {
        $question = Question::create($request->only(['quiz_id', 'text', 'type']));

        return new QuestionResource($question);
    }

    public function show(Question $question): QuestionResource
    {
        return new QuestionResource($question);
    }

    public function update(Request $request, Question $question): QuestionResource
    {
        $question->update($request->only(['quiz_id', 'text', 'type']));
        return new QuestionResource($question);
    }

    public function destroy(Question $question): JsonResponse
    {
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully.']);
    }
}
