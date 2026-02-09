<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Issue</title>
</head>
<body style="font-family: sans-serif; max-width: 700px; margin: 2rem auto;">
<h1>Report Issue</h1>
<p><a href="{{ route('issues.index') }}">Back to list</a></p>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="post" action="{{ route('issues.store') }}">
    @csrf
    <div style="margin-bottom: 0.75rem;">
        <label for="title">Title</label><br>
        <input id="title" name="title" value="{{ old('title') }}" required style="width: 100%;">
    </div>

    <div style="margin-bottom: 0.75rem;">
        <label for="description">Description</label><br>
        <textarea id="description" name="description" rows="6" required style="width: 100%;">{{ old('description') }}</textarea>
    </div>

    <div style="margin-bottom: 0.75rem;">
        <label for="priority">Priority</label><br>
        <select id="priority" name="priority">
            @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>{{ $priority }}</option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 0.75rem;">
        <label for="reported_by">Reported by</label><br>
        <input id="reported_by" name="reported_by" value="{{ old('reported_by') }}">
    </div>

    <button type="submit">Submit Issue</button>
</form>
</body>
</html>
