<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
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

        $labels = Label::factory()->count(8)->create();

        Project::factory()
            ->count(5)
            ->create()
            ->each(function (Project $project) use ($labels): void {
                Task::factory()
                    ->count(6)
                    ->create([
                        'project_id' => $project->id,
                    ])
                    ->each(function (Task $task) use ($labels): void {
                        $labelIds = $labels->random(rand(1, 3))->pluck('id')->all();
                        $task->labels()->sync($labelIds);
                    });
            });
    }
}
