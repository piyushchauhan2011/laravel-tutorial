<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Task::query()->with(['project', 'labels'])->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
            'priority' => ['required', 'integer', 'min:1', 'max:3'],
            'due_date' => ['nullable', 'date'],
            'label_ids' => ['array'],
            'label_ids.*' => ['exists:labels,id'],
        ]);

        $task = Task::query()->create($validated);
        $task->labels()->sync($validated['label_ids'] ?? []);

        return response()->json([
            'data' => $task->load(['project', 'labels']),
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => $task->load(['project', 'labels']),
        ]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'required', 'exists:projects,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:todo,in_progress,done'],
            'priority' => ['sometimes', 'required', 'integer', 'min:1', 'max:3'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'label_ids' => ['array'],
            'label_ids.*' => ['exists:labels,id'],
        ]);

        $task->update($validated);
        if (array_key_exists('label_ids', $validated)) {
            $task->labels()->sync($validated['label_ids']);
        }

        return response()->json([
            'data' => $task->fresh()->load(['project', 'labels']),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([], 204);
    }
}
