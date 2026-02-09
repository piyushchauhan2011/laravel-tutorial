<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Role;
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
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $memberRole = Role::query()->create([
            'name' => 'Member',
            'slug' => 'member',
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $memberUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $adminUser->roles()->attach($adminRole);
        $memberUser->roles()->attach($memberRole);

        $projects = Project::factory()
            ->count(2)
            ->for($memberUser)
            ->create();

        foreach ($projects as $project) {
            Task::factory()->count(3)->for($project)->create();
        }
    }
}
