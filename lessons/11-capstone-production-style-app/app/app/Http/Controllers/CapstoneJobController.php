<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostRequest;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CapstoneJobController extends Controller
{
    public function index(): View
    {
        return view('jobs.index', [
            'jobs' => JobPost::query()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('jobs.create');
    }

    public function store(StoreJobPostRequest $request): RedirectResponse
    {
        $job = JobPost::query()->create([
            ...$request->validated(),
            'is_remote' => $request->boolean('is_remote'),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('jobs.show', $job)
            ->with('status', 'Job post created.');
    }

    public function show(JobPost $job): View
    {
        return view('jobs.show', [
            'job' => $job->load(['applications.statusEvents']),
        ]);
    }
}
