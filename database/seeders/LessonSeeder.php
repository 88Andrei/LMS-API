<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::first();

        for ($i=0; $i < 5; $i++) { 
            Lesson::create([
                'course_id' => $course->id,
                'title' => 'Lesson ' . ($i + 1),
                'content' => 'This is the content for lesson ' . ($i + 1),
                'order' => $i + 1,
            ]);
        }
    }
}
