<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveIssueRequest;
use App\Http\Requests\StoreIssueRequest;
use App\Jobs\AssessIssueSeverity;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(): View
    {
        return view('issues.index', [
            'issues' => Issue::query()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('issues.create');
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $issue = Issue::query()->create([
            ...$request->validated(),
            'status' => 'open',
        ]);

        AssessIssueSeverity::dispatch($issue->id);

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue reported and queued for severity assessment.');
    }

    public function show(Issue $issue): View
    {
        return view('issues.show', [
            'issue' => $issue,
        ]);
    }

    public function resolve(ResolveIssueRequest $request, Issue $issue): RedirectResponse
    {
        $issue->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue marked as resolved.');
    }
}
