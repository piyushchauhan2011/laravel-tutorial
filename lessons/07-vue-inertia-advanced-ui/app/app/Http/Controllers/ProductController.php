<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'featured' => ['nullable', Rule::in(['1', '0'])],
            'sort' => ['nullable', Rule::in(['name', 'price', 'created_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $queryText = $validated['q'] ?? null;
        $status = $validated['status'] ?? null;
        $featured = $validated['featured'] ?? null;
        $sort = $validated['sort'] ?? 'created_at';
        $direction = $validated['direction'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 10;

        $products = Product::query()
            ->when($queryText, function (Builder $query, string $search): void {
                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'ilike', '%'.$search.'%')
                        ->orWhere('sku', 'ilike', '%'.$search.'%')
                        ->orWhere('description', 'ilike', '%'.$search.'%');
                });
            })
            ->when($status, fn (Builder $query, string $value) => $query->where('status', $value))
            ->when($featured !== null, fn (Builder $query) => $query->where('is_featured', $featured === '1'))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'status' => $product->status,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_featured' => $product->is_featured,
                'created_at' => $product->created_at?->toDateString(),
            ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => [
                'q' => $queryText,
                'status' => $status,
                'featured' => $featured,
                'per_page' => $perPage,
            ],
            'sort' => [
                'field' => $sort,
                'direction' => $direction,
            ],
            'statusOptions' => ['draft', 'published', 'archived'],
        ]);
    }

    public function show(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'status' => $product->status,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_featured' => $product->is_featured,
                'created_at' => $product->created_at?->toDateTimeString(),
                'updated_at' => $product->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    public function toggleFeatured(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->update([
            'is_featured' => ! $product->is_featured,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $product->id,
                    'is_featured' => $product->is_featured,
                ],
            ]);
        }

        return back();
    }
}
