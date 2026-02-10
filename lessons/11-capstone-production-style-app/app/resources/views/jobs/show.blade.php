<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $job->title }}</title>
</head>
<body style="font-family: sans-serif; max-width: 1000px; margin: 2rem auto;">
<h1>{{ $job->title }}</h1>
<p>
    <a href="{{ route('jobs.index') }}">Back to jobs</a> |
    <a href="{{ route('capstone.dashboard') }}">Feature flags</a>
</p>

@if (session('status'))
    <p style="padding: 0.75rem; border: 1px solid #ddd;">{{ session('status') }}</p>
@endif

<p><strong>Department:</strong> {{ $job->department }}</p>
<p><strong>Location:</strong> {{ $job->location }}{{ $job->is_remote ? ' (remote allowed)' : '' }}</p>
<p><strong>Status:</strong> {{ $job->status }}</p>
<p><strong>Description:</strong><br>{{ $job->description }}</p>

<h2>Submit Application</h2>
@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<form method="post" action="{{ route('jobs.applications.store', $job) }}">
    @csrf
    <p><label>Name<br><input name="candidate_name" value="{{ old('candidate_name') }}" required></label></p>
    <p><label>Email<br><input name="email" value="{{ old('email') }}" required></label></p>
    <p>
        <label>Source<br>
            <select name="source">
                @foreach (['career_site', 'referral', 'agency', 'linkedin'] as $source)
                    <option value="{{ $source }}" @selected(old('source', 'career_site') === $source)>{{ $source }}</option>
                @endforeach
            </select>
        </label>
    </p>
    <p><label>Years experience<br><input name="years_experience" value="{{ old('years_experience', 3) }}" required></label></p>
    <p><label>Resume text<br><textarea name="resume_text" rows="4" required>{{ old('resume_text') }}</textarea></label></p>
    <p><label>Cover letter<br><textarea name="cover_letter" rows="3">{{ old('cover_letter') }}</textarea></label></p>
    <button type="submit">Submit Application</button>
</form>

<h2>Applications</h2>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Candidate</th>
        <th>Stage</th>
        <th>Score</th>
        <th>Source</th>
        <th>Update Stage</th>
    </tr>
    </thead>
    <tbody>
    @forelse($job->applications as $application)
        <tr>
            <td>{{ $application->candidate_name }}</td>
            <td>{{ $application->stage }}</td>
            <td>{{ $application->fit_score ?? 'pending' }}</td>
            <td>{{ $application->source }}</td>
            <td>
                <form method="post" action="{{ route('jobs.applications.stage', [$job, $application]) }}">
                    @csrf
                    @method('patch')
                    <select name="stage">
                        @foreach (\App\Models\Application::stages() as $stage)
                            <option value="{{ $stage }}">{{ $stage }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5">No applications yet.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
