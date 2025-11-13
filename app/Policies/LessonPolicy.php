<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    public function create(User $user, Course $course): bool
    {
        return $user->role === UserRole::TEACHER 
            && $course->teacher_id === $user->id;
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->course->teacher_id;
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->course->teacher_id;
    }
}
