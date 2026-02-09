<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $sort = $request->string('sort', 'created_at')->value();
        $direction = strtolower($request->string('direction', 'desc')->value());
        $statusFilter = $request->input('filter.status');

        if (! in_array($sort, ['name', 'status', 'created_at'], true)) {
            $sort = 'created_at';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query = Project::query()
            ->where('user_id', $request->user()->id)
            ->when($statusFilter, fn (Builder $builder) => $builder->where('status', $statusFilter))
            ->orderBy($sort, $direction);

        $projects = $query->paginate($perPage)->withQueryString();

        return ApiResponse::success($projects->items(), [
            'pagination' => [
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
            ],
            'filters' => [
                'status' => $statusFilter,
            ],
            'sort' => [
                'field' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        $project = $request->user()->projects()->create($validated);

        return ApiResponse::success($project, [], 201);
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        $project = Project::query()
            ->where('id', $project->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return ApiResponse::success($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $project = Project::query()
            ->where('id', $project->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:draft,active,archived'],
        ]);

        $project->update($validated);

        return ApiResponse::success($project->fresh());
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $project = Project::query()
            ->where('id', $project->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $project->delete();

        return ApiResponse::success([
            'message' => 'Project deleted.',
        ]);
    }
}
