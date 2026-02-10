<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobPost;
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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $jobs = JobPost::factory()->count(3)->create([
            'status' => 'open',
        ]);

        foreach ($jobs as $job) {
            Application::factory()->count(5)->create([
                'job_post_id' => $job->id,
            ]);
        }
    }
}
