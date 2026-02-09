<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsCteTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_endpoint_returns_grouped_metrics_with_window_fields(): void
    {
        $category = Category::query()->create(['name' => 'Hardware']);

        Sale::query()->insert([
            ['category_id' => $category->id, 'amount' => 100.00, 'sold_at' => '2026-02-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $category->id, 'amount' => 120.00, 'sold_at' => '2026-02-01 12:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $category->id, 'amount' => 80.00, 'sold_at' => '2026-02-02 09:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->getJson('/analytics/revenue?from=2026-02-01&to=2026-02-03&group_by=day');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['bucket_start', 'revenue', 'sales_count', 'running_revenue', 'revenue_rank'],
            ],
            'meta' => ['filters' => ['from', 'to', 'group_by']],
        ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals(220.0, (float) $data[0]['revenue']);
        $this->assertEquals(300.0, (float) $data[1]['running_revenue']);
    }

    public function test_revenue_endpoint_rejects_invalid_group_by(): void
    {
        $response = $this->getJson('/analytics/revenue?from=2026-02-01&to=2026-02-03&group_by=hour');

        $response->assertStatus(422);
    }

    public function test_recursive_category_rollup_returns_ranked_tree_revenue(): void
    {
        $hardware = Category::query()->create(['name' => 'Hardware']);
        $software = Category::query()->create(['name' => 'Software']);

        $laptops = Category::query()->create(['name' => 'Laptops', 'parent_id' => $hardware->id]);
        $saas = Category::query()->create(['name' => 'SaaS', 'parent_id' => $software->id]);

        Sale::query()->insert([
            ['category_id' => $laptops->id, 'amount' => 300.00, 'sold_at' => '2026-02-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $saas->id, 'amount' => 100.00, 'sold_at' => '2026-02-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->getJson('/analytics/category-rollup?from=2026-02-01&to=2026-02-28');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['root_category_id', 'root_name', 'tree_revenue', 'category_count', 'max_depth', 'revenue_rank'],
            ],
            'meta' => ['filters' => ['from', 'to']],
        ]);

        $data = collect($response->json('data'));
        $hardwareRow = $data->firstWhere('root_name', 'Hardware');
        $softwareRow = $data->firstWhere('root_name', 'Software');

        $this->assertNotNull($hardwareRow);
        $this->assertNotNull($softwareRow);
        $this->assertEquals(300.0, (float) $hardwareRow['tree_revenue']);
        $this->assertEquals(1, (int) $hardwareRow['revenue_rank']);
    }
}
