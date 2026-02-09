<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $editor = User::factory()->editor()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ]);

        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => 'member',
        ]);

        Post::factory()->count(8)->create([
            'author_id' => $admin->id,
            'status' => 'published',
        ]);

        Post::factory()->count(5)->create([
            'author_id' => $editor->id,
            'status' => 'review',
        ]);

        Post::factory()->count(7)->create([
            'status' => 'draft',
        ]);
    }
}
