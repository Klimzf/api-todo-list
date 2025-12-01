<?php
namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_task()
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
            'status' => 'pending'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    /** @test */
    public function prevents_invalid_status()
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Bad Task',
            'status' => 'invalid'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function can_delete_task()
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function can_update_task()
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Task',
            'status' => 'completed'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }
}
