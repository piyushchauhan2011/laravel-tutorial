<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('requires authentication for products index', function () {
    $this->get('/products')
        ->assertRedirect('/login');
});

it('filters and sorts the products index', function () {
    $user = User::factory()->create();

    Product::factory()->create([
        'name' => 'Alpha Widget',
        'status' => 'published',
        'price' => 100.00,
        'is_featured' => true,
    ]);

    Product::factory()->create([
        'name' => 'Beta Widget',
        'status' => 'published',
        'price' => 50.00,
        'is_featured' => false,
    ]);

    Product::factory()->create([
        'name' => 'Archived Widget',
        'status' => 'archived',
        'price' => 500.00,
        'is_featured' => true,
    ]);

    $this->actingAs($user)
        ->get('/products?status=published&featured=1&sort=price&direction=desc')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Alpha Widget')
            ->where('products.data.0.status', 'published')
            ->where('products.data.0.is_featured', true)
            ->where('sort.field', 'price')
            ->where('sort.direction', 'desc')
            ->where('filters.status', 'published'));
});

it('supports pagination controls', function () {
    $user = User::factory()->create();

    Product::factory()->count(21)->create();

    $this->actingAs($user)
        ->get('/products?per_page=10&page=2')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->where('products.current_page', 2)
            ->where('products.per_page', 10));
});

it('toggles featured state via json endpoint', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'is_featured' => false,
    ]);

    $this->actingAs($user)
        ->patchJson(route('products.toggle-featured', $product))
        ->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.is_featured', true);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'is_featured' => true,
    ]);
});
