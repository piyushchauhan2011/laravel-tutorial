@extends('layouts.capstone')

@section('title', $job->title)

@section('content')
<div class="cap-card mb-4">
    <div class="cap-card__header d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">{{ $job->title }}</h1>
        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-secondary">Back to jobs</a>
    </div>
    <div class="cap-card__body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Department:</strong><br>{{ $job->department }}</div>
            <div class="col-md-3"><strong>Location:</strong><br>{{ $job->location }}{{ $job->is_remote ? ' (remote)' : '' }}</div>
            <div class="col-md-2"><strong>Status:</strong><br>{{ $job->status }}</div>
            <div class="col-md-4"><strong>Comp:</strong><br>{{ $job->salary_min ?? '-' }} - {{ $job->salary_max ?? '-' }}</div>
            <div class="col-12"><strong>Description:</strong><br>{{ $job->description }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="cap-card h-100">
            <div class="cap-card__header">
                <div class="cap-section-title mb-0">Submit Application</div>
            </div>
            <div class="cap-card__body">
@if ($errors->any())
    <ul class="mb-3">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<form method="post" action="{{ route('jobs.applications.store', $job) }}">
    @csrf
    <div class="mb-3">
        <label class="cap-form__label">Name</label>
        <input class="form-control" name="candidate_name" value="{{ old('candidate_name') }}" required>
    </div>
    <div class="mb-3">
        <label class="cap-form__label">Email</label>
        <input class="form-control" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
        <label class="cap-form__label">Source</label>
        <select class="form-select" name="source">
                @foreach (['career_site', 'referral', 'agency', 'linkedin'] as $source)
                    <option value="{{ $source }}" @selected(old('source', 'career_site') === $source)>{{ $source }}</option>
                @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="cap-form__label">Years experience</label>
        <input class="form-control" name="years_experience" value="{{ old('years_experience', 3) }}" required>
    </div>
    <div class="mb-3">
        <label class="cap-form__label">Resume text</label>
        <textarea class="form-control" name="resume_text" rows="4" required>{{ old('resume_text') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="cap-form__label">Cover letter</label>
        <textarea class="form-control" name="cover_letter" rows="3">{{ old('cover_letter') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Application</button>
</form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="cap-card h-100">
            <div class="cap-card__header">
                <div class="cap-section-title mb-0">Applications</div>
            </div>
            <div class="cap-card__body p-0">
<table class="table table-striped table-hover cap-table mb-0">
    <thead>
    <tr>
        <th class="px-3">Candidate</th>
        <th>Stage</th>
        <th>Score</th>
        <th>Source</th>
        <th>Update Stage</th>
    </tr>
    </thead>
    <tbody>
    @forelse($job->applications as $application)
        <tr>
            <td class="px-3">{{ $application->candidate_name }}</td>
            <td>{{ $application->stage }}</td>
            <td>{{ $application->fit_score ?? 'pending' }}</td>
            <td>{{ $application->source }}</td>
            <td>
                <form method="post" action="{{ route('jobs.applications.stage', [$job, $application]) }}">
                    @csrf
                    @method('patch')
                    <select name="stage" class="form-select form-select-sm d-inline-block w-auto">
                        @foreach (\App\Models\Application::stages() as $stage)
                            <option value="{{ $stage }}">{{ $stage }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td class="px-3" colspan="5">No applications yet.</td>
        </tr>
    @endforelse
    </tbody>
</table>
            </div>
        </div>
    </div>
</div>
@endsection
