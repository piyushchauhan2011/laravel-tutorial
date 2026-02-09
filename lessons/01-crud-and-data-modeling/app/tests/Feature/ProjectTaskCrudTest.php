<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTaskCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_project(): void
    {
        $response = $this->postJson('/projects', [
            'name' => 'Website Redesign',
            'description' => 'Revamp marketing site',
            'status' => 'active',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Website Redesign');

        $this->assertDatabaseHas('projects', [
            'name' => 'Website Redesign',
        ]);
    }

    public function test_can_create_task_with_labels(): void
    {
        $project = Project::factory()->create();
        $labels = Label::factory()->count(2)->create();

        $response = $this->postJson('/tasks', [
            'project_id' => $project->id,
            'title' => 'Implement dashboard cards',
            'status' => 'todo',
            'priority' => 2,
            'label_ids' => $labels->pluck('id')->all(),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.project.id', $project->id)
            ->assertJsonCount(2, 'data.labels');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Implement dashboard cards',
            'project_id' => $project->id,
        ]);
    }

    public function test_validation_error_when_task_title_missing(): void
    {
        $project = Project::factory()->create();

        $response = $this->postJson('/tasks', [
            'project_id' => $project->id,
            'status' => 'todo',
            'priority' => 2,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_update_and_delete_task(): void
    {
        $task = Task::factory()->create([
            'status' => 'todo',
            'priority' => 1,
        ]);

        $update = $this->patchJson("/tasks/{$task->id}", [
            'status' => 'done',
            'priority' => 3,
        ]);

        $update->assertOk()
            ->assertJsonPath('data.status', 'done')
            ->assertJsonPath('data.priority', 3);

        $delete = $this->deleteJson("/tasks/{$task->id}");
        $delete->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
