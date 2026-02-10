<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Capstone Jobs</title>
</head>
<body style="font-family: sans-serif; max-width: 1000px; margin: 2rem auto;">
<h1>Hiring Jobs</h1>
<p>
    <a href="{{ route('jobs.create') }}">Create job</a> |
    <a href="{{ route('capstone.dashboard') }}">Feature flags</a>
</p>

@if (session('status'))
    <p style="padding: 0.75rem; border: 1px solid #ddd;">{{ session('status') }}</p>
@endif

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Title</th>
        <th>Department</th>
        <th>Status</th>
        <th>Location</th>
    </tr>
    </thead>
    <tbody>
    @forelse($jobs as $job)
        <tr>
            <td><a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></td>
            <td>{{ $job->department }}</td>
            <td>{{ $job->status }}</td>
            <td>{{ $job->location }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">No jobs yet.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top: 1rem;">
    {{ $jobs->links() }}
</div>
</body>
</html>
