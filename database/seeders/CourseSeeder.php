<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $students = User::factory()->count(5)->create(['role' => 'student']);

        $course = Course::create([
            'title' => 'Introduction to Programming',
            'description' => 'Learn the basics of programming using Php.',
            'teacher_id' => $teacher->id,
        ]);

        foreach ($students as $student) {
            $course->users()->attach($student->id, ['role' => 'student', 'progress' => rand(0, 100)]);
        }

        $course->users()->attach($teacher->id, ['role' => 'teacher']);
    }

}
