<?php

use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

function userWithRole(string $slug): User
{
    $role = Role::query()->firstOrCreate(
        ['slug' => $slug],
        ['name' => ucfirst($slug)]
    );

    $user = User::factory()->create();
    $user->roles()->attach($role);

    return $user;
}

test('project owner can create and update own project', function () {
    $owner = userWithRole('member');

    $createResponse = $this->actingAs($owner)->postJson('/projects', [
        'name' => 'Owner Project',
        'description' => 'Owned by current user',
        'status' => 'active',
    ]);

    $createResponse->assertCreated();

    $projectId = $createResponse->json('data.id');

    $updateResponse = $this->actingAs($owner)->patchJson("/projects/{$projectId}", [
        'status' => 'archived',
    ]);

    $updateResponse->assertOk();
    expect($updateResponse->json('data.status'))->toBe('archived');
});

test('non owner cannot update another users project', function () {
    $owner = userWithRole('member');
    $other = userWithRole('member');

    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($other)->patchJson("/projects/{$project->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertForbidden();
});

test('admin can delete another users project', function () {
    $owner = userWithRole('member');
    $admin = userWithRole('admin');

    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($admin)->deleteJson("/projects/{$project->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('member cannot create task in another users project', function () {
    $owner = userWithRole('member');
    $other = userWithRole('member');

    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($other)->postJson('/tasks', [
        'project_id' => $project->id,
        'title' => 'Illegal task',
        'status' => 'todo',
    ]);

    $response->assertForbidden();
});

test('owner can create and update task in own project', function () {
    $owner = userWithRole('member');
    $project = Project::factory()->for($owner)->create();

    $createResponse = $this->actingAs($owner)->postJson('/tasks', [
        'project_id' => $project->id,
        'title' => 'Initial task',
        'status' => 'todo',
    ]);

    $createResponse->assertCreated();
    $task = Task::query()->findOrFail($createResponse->json('data.id'));

    $updateResponse = $this->actingAs($owner)->patchJson("/tasks/{$task->id}", [
        'status' => 'done',
    ]);

    $updateResponse->assertOk();
    expect($updateResponse->json('data.status'))->toBe('done');
});
