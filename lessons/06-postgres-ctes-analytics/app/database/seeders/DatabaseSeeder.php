<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Sale;
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

        $hardware = Category::query()->create(['name' => 'Hardware']);
        $software = Category::query()->create(['name' => 'Software']);

        $laptops = Category::query()->create(['name' => 'Laptops', 'parent_id' => $hardware->id]);
        $accessories = Category::query()->create(['name' => 'Accessories', 'parent_id' => $hardware->id]);
        $saas = Category::query()->create(['name' => 'SaaS', 'parent_id' => $software->id]);

        Sale::query()->insert([
            ['category_id' => $laptops->id, 'amount' => 1200.00, 'sold_at' => now()->subDays(9), 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $laptops->id, 'amount' => 950.00, 'sold_at' => now()->subDays(7), 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $accessories->id, 'amount' => 300.00, 'sold_at' => now()->subDays(6), 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $saas->id, 'amount' => 500.00, 'sold_at' => now()->subDays(5), 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $saas->id, 'amount' => 650.00, 'sold_at' => now()->subDays(2), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
