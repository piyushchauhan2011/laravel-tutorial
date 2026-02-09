<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_note(): void
    {
        $response = $this->postJson('/notes', [
            'title' => 'First note',
            'body' => 'Created from feature test',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'First note');

        $this->assertDatabaseHas('notes', [
            'title' => 'First note',
        ]);
    }

    public function test_can_list_notes(): void
    {
        Note::factory()->count(2)->create();

        $response = $this->getJson('/notes');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_update_a_note(): void
    {
        $note = Note::factory()->create([
            'title' => 'Old title',
        ]);

        $response = $this->patchJson("/notes/{$note->id}", [
            'title' => 'New title',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'New title');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'New title',
        ]);
    }

    public function test_can_delete_a_note(): void
    {
        $note = Note::factory()->create();

        $response = $this->deleteJson("/notes/{$note->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
