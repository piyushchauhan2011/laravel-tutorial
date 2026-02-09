<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function issueTokenFor(User $user): string
    {
        $response = $this->postJson('/api/v1/tokens', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertCreated();

        return $response->json('data.access_token');
    }

    public function test_can_issue_token_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/tokens', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'macbook',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => ['token_type', 'access_token'],
                'meta' => ['user' => ['id', 'name', 'email']],
            ]);
    }

    public function test_invalid_credentials_return_contract_error(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/tokens', [
            'email' => $user->email,
            'password' => 'not-the-password',
            'device_name' => 'macbook',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson([
                'error' => [
                    'code' => 'unauthenticated',
                    'message' => 'Invalid credentials.',
                ],
            ]);
    }

    public function test_projects_endpoint_requires_token(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'error' => [
                    'code' => 'unauthenticated',
                ],
            ]);
    }

    public function test_can_create_and_list_projects_with_token_and_query_params(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(2)->for($user)->create(['status' => 'active']);
        Project::factory()->count(1)->for($user)->create(['status' => 'archived']);

        $token = $this->issueTokenFor($user);

        $create = $this->withToken($token)->postJson('/api/v1/projects', [
            'name' => 'API Project',
            'description' => 'Created through API',
            'status' => 'active',
        ]);

        $create->assertCreated()->assertJsonPath('data.name', 'API Project');

        $list = $this->withToken($token)->getJson('/api/v1/projects?per_page=2&sort=name&direction=asc&filter[status]=active');

        $list
            ->assertOk()
            ->assertJsonPath('meta.pagination.per_page', 2)
            ->assertJsonPath('meta.filters.status', 'active')
            ->assertJsonPath('meta.sort.field', 'name')
            ->assertJsonPath('meta.sort.direction', 'asc');

        $this->assertCount(2, $list->json('data'));
    }

    public function test_validation_error_matches_contract(): void
    {
        $user = User::factory()->create();
        $token = $this->issueTokenFor($user);

        $response = $this->withToken($token)->postJson('/api/v1/projects', [
            'description' => 'Missing name and status',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'details'],
            ]);
    }

    public function test_cannot_read_or_update_other_users_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $project = Project::factory()->for($owner)->create();
        $token = $this->issueTokenFor($other);

        $showResponse = $this->withToken($token)->getJson("/api/v1/projects/{$project->id}");
        $showResponse->assertNotFound()->assertJsonPath('error.code', 'resource_not_found');

        $updateResponse = $this->withToken($token)->patchJson("/api/v1/projects/{$project->id}", [
            'name' => 'Hijacked',
        ]);
        $updateResponse->assertNotFound()->assertJsonPath('error.code', 'resource_not_found');
    }

    public function test_can_revoke_current_token(): void
    {
        $user = User::factory()->create();
        $token = $this->issueTokenFor($user);

        $response = $this->withToken($token)->deleteJson('/api/v1/tokens/current');

        $response
            ->assertOk()
            ->assertJsonPath('data.message', 'Current token revoked.');
    }
}
