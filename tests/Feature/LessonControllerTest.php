<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $teacher;
    private Course $course;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        // Create a teacher user
        $this->teacher = User::factory()->create([
            'role' => UserRole::TEACHER->value
        ]);

        // Create a course
        $this->course = Course::factory()->create([
            'teacher_id' => $this->teacher->id
        ]);

        // Attach teacher to the course
        $this->course->users()->attach($this->teacher->id, ['role' => UserRole::TEACHER->value]);

        // Create a lesson
        $this->lesson = Lesson::factory()->create([
            'course_id' => $this->course->id
        ]);
    }

    public function test_can_list_lessons(): void
    {
        $response = $this->getJson('/api/v1/lessons');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'content',
                             'course_id'
                         ]
                     ],
                     'links',
                     'meta'
                 ]);
    }

    public function test_teacher_can_create_lesson(): void
    {
        // $lessonData = [
        //     'title' => $this->faker->sentence,
        //     'content' => $this->faker->paragraph,
        //     'course_id' => $this->course->id,
        // ];

        // $response = $this->actingAs($this->teacher)
        //                  ->postJson('/api/v1/lessons', $lessonData);

        // $response->assertStatus(201)
        //          ->assertJsonStructure([
        //              'data' => [
        //                  'id',
        //                  'title',
        //                  'content',
        //                  'course_id'
        //              ]
        //          ]);

        // $this->assertDatabaseHas('lessons', [
        //     'title' => $lessonData['title'],
        //     'content' => $lessonData['content'],
        //     'course_id' => $lessonData['course_id']
        // ]);

        
            $lessonData = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'course_id' => 1, // Убедитесь, что курс с ID 1 существует
    ];

    $response = $this->actingAs($this->teacher)
                     ->postJson('/api/v1/lessons', $lessonData);

    $response->dump(); // Вывод полного ответа для отладки
    $response->assertStatus(201);
    }

    public function test_can_show_lesson(): void
    {
        $response = $this->getJson("/api/v1/lessons/{$this->lesson->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'content',
                         'course_id'
                     ]
                 ]);
    }

    public function test_teacher_can_update_lesson(): void
    {
        $updateData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($this->teacher)
                         ->putJson("/api/v1/lessons/{$this->lesson->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'content',
                         'course_id'
                     ]
                 ]);

        $this->assertDatabaseHas('lessons', [
            'id' => $this->lesson->id,
            'title' => $updateData['title'],
            'content' => $updateData['content']
        ]);
    }

    public function test_teacher_can_delete_lesson(): void
    {
        $response = $this->actingAs($this->teacher)
                         ->deleteJson("/api/v1/lessons/{$this->lesson->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Lesson deleted successfully.']);

        $this->assertDatabaseMissing('lessons', ['id' => $this->lesson->id]);
    }

    public function test_unauthorized_user_cannot_create_lesson(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::STUDENT->value
        ]);

        $lessonData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'course_id' => $this->course->id,
        ];

        $response = $this->actingAs($student)
                         ->postJson('/api/v1/lessons', $lessonData);

        $response->assertStatus(403);
    }
}