<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $policy = new PostPolicy();

        $this->assertTrue($policy->delete($admin, $post));
    }

    public function test_editor_cannot_delete_posts(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create();

        $policy = new PostPolicy();

        $this->assertFalse($policy->delete($editor, $post));
    }

    public function test_editor_can_update_only_their_own_posts(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();

        $ownPost = Post::factory()->create(['author_id' => $editor->id]);
        $otherPost = Post::factory()->create(['author_id' => $otherEditor->id]);

        $policy = new PostPolicy();

        $this->assertTrue($policy->update($editor, $ownPost));
        $this->assertFalse($policy->update($editor, $otherPost));
    }
}
