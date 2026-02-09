<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $query = Project::query()->withCount('tasks');

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json([
            'data' => $query->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        $project = $request->user()->projects()->create($validated);

        return response()->json(['data' => $project], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json([
            'data' => $project->load('tasks'),
        ]);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:draft,active,archived'],
        ]);

        $project->update($validated);

        return response()->json(['data' => $project->fresh()]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([], 204);
    }
}
