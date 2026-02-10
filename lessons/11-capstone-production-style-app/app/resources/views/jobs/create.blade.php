<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Job</title>
</head>
<body style="font-family: sans-serif; max-width: 760px; margin: 2rem auto;">
<h1>Create Job</h1>
<p><a href="{{ route('jobs.index') }}">Back to jobs</a></p>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="post" action="{{ route('jobs.store') }}">
    @csrf
    <p>
        <label for="title">Title</label><br>
        <input id="title" name="title" value="{{ old('title') }}" required style="width:100%;">
    </p>
    <p>
        <label for="department">Department</label><br>
        <input id="department" name="department" value="{{ old('department') }}" required style="width:100%;">
    </p>
    <p>
        <label for="location">Location</label><br>
        <input id="location" name="location" value="{{ old('location') }}" required style="width:100%;">
    </p>
    <p>
        <label for="employment_type">Type</label><br>
        <select id="employment_type" name="employment_type">
            @foreach (['full_time', 'contract', 'internship'] as $type)
                <option value="{{ $type }}" @selected(old('employment_type', 'full_time') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </p>
    <p>
        <label for="status">Status</label><br>
        <select id="status" name="status">
            @foreach (['draft', 'open', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', 'open') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </p>
    <p>
        <label><input type="checkbox" name="is_remote" value="1" @checked(old('is_remote'))> Remote allowed</label>
    </p>
    <p>
        <label for="salary_min">Salary min</label><br>
        <input id="salary_min" name="salary_min" value="{{ old('salary_min') }}">
    </p>
    <p>
        <label for="salary_max">Salary max</label><br>
        <input id="salary_max" name="salary_max" value="{{ old('salary_max') }}">
    </p>
    <p>
        <label for="description">Description</label><br>
        <textarea id="description" name="description" rows="6" style="width:100%;" required>{{ old('description') }}</textarea>
    </p>
    <button type="submit">Create Job</button>
</form>
</body>
</html>
