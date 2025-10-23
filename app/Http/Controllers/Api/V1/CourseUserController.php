<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignUserToCourseRequest;
use App\Models\Course;

class CourseUserController extends Controller
{
    public function assignUser(AssignUserToCourseRequest $request, Course $course)
    {
        $course->users()->syncWithoutDetaching([
            $request->user_id => [
                'role' => $request->role,
                'progress' => $request->progress,
            ]
        ]);

        return response()->json(['message' => 'User assigned to course']);
    }
}
