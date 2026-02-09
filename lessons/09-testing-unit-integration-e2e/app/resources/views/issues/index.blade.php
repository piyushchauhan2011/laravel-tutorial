<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Issues</title>
</head>
<body style="font-family: sans-serif; max-width: 900px; margin: 2rem auto;">
<h1>Issue Tracker</h1>
<p><a href="{{ route('issues.create') }}">Report new issue</a></p>

@if (session('status'))
    <p>{{ session('status') }}</p>
@endif

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Severity</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($issues as $issue)
        <tr>
            <td>{{ $issue->id }}</td>
            <td><a href="{{ route('issues.show', $issue) }}">{{ $issue->title }}</a></td>
            <td>{{ $issue->priority }}</td>
            <td>{{ $issue->status }}</td>
            <td>{{ $issue->severity_score }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5">No issues yet.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top: 1rem;">
    {{ $issues->links() }}
</div>
</body>
</html>
