<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Issue {{ $issue->id }}</title>
</head>
<body style="font-family: sans-serif; max-width: 700px; margin: 2rem auto;">
<h1>{{ $issue->title }}</h1>
<p><a href="{{ route('issues.index') }}">Back to list</a></p>

@if (session('status'))
    <p>{{ session('status') }}</p>
@endif

<p><strong>Priority:</strong> {{ $issue->priority }}</p>
<p><strong>Status:</strong> {{ $issue->status }}</p>
<p><strong>Severity score:</strong> {{ $issue->severity_score }}</p>
<p><strong>Reported by:</strong> {{ $issue->reported_by ?? 'Anonymous' }}</p>
<p><strong>Description:</strong><br>{{ $issue->description }}</p>

@if ($issue->status !== 'resolved')
    <form method="post" action="{{ route('issues.resolve', $issue) }}">
        @csrf
        @method('patch')
        <button type="submit">Mark as resolved</button>
    </form>
@endif
</body>
</html>
