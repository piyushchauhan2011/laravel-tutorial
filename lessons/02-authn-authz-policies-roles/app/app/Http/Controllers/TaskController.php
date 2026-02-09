<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::query()->with('project');

        if (! $request->user()->isAdmin()) {
            $query->whereHas('project', function ($projectQuery) use ($request) {
                $projectQuery->where('user_id', $request->user()->id);
            });
        }

        return response()->json([
            'data' => $query->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);

        abort_unless(
            $request->user()->isAdmin() || $project->user_id === $request->user()->id,
            403
        );

        $task = Task::query()->create($validated);

        return response()->json(['data' => $task], 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json([
            'data' => $task->load('project'),
        ]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:todo,in_progress,done'],
        ]);

        $task->update($validated);

        return response()->json(['data' => $task->fresh()]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([], 204);
    }
}
