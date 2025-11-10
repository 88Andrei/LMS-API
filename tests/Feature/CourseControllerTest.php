<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $teacher;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        
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
    }

    public function test_can_list_courses(): void
    {
        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'teacher',
                            'lessons_count'
                        ]
                    ],
                    'links',
                    'meta'
                ]);
    }

    public function test_teacher_can_create_course(): void
    {
        $courseData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($this->teacher)
                        ->postJson('/api/v1/courses', $courseData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'teacher'
                    ]
                ]);

        $this->assertDatabaseHas('courses', [
            'title' => $courseData['title'],
            'description' => $courseData['description'],
            'teacher_id' => $this->teacher->id
        ]);
    }

    public function test_can_show_course(): void
    {
        $response = $this->getJson("/api/v1/courses/{$this->course->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'teacher',
                        'lessons'
                    ]
                ]);
    }

    public function test_teacher_can_update_own_course(): void
    {
        $updateData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($this->teacher)
                        ->putJson("/api/v1/courses/{$this->course->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'teacher'
                    ]
                ]);

        $this->assertDatabaseHas('courses', [
            'id' => $this->course->id,
            'title' => $updateData['title'],
            'description' => $updateData['description']
        ]);
    }

    public function test_teacher_cannot_update_others_course(): void
    {
        $otherTeacher = User::factory()->create([
            'role' => UserRole::TEACHER->value
        ]);

        $updateData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($otherTeacher)
                        ->putJson("/api/v1/courses/{$this->course->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_teacher_can_delete_own_course(): void
    {
        $response = $this->actingAs($this->teacher)
                        ->deleteJson("/api/v1/courses/{$this->course->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Course deleted successfully.']);

        $this->assertDatabaseMissing('courses', ['id' => $this->course->id]);
    }

    public function test_teacher_cannot_delete_others_course(): void
    {
        $otherTeacher = User::factory()->create([
            'role' => UserRole::TEACHER->value
        ]);

        $response = $this->actingAs($otherTeacher)
                        ->deleteJson("/api/v1/courses/{$this->course->id}");

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_create_course(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::STUDENT->value
        ]);

        $courseData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($student)
                        ->postJson('/api/v1/courses', $courseData);

        $response->assertStatus(403);
    }
}