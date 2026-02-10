@extends('layouts.capstone')

@section('title', 'Jobs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Hiring Jobs</h1>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary">Create job</a>
</div>

<div class="cap-card">
    <div class="cap-card__body p-0">
        <table class="table table-striped table-hover cap-table mb-0">
            <thead>
            <tr>
                <th class="px-3">Title</th>
                <th>Department</th>
                <th>Status</th>
                <th>Location</th>
            </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
                <tr>
                    <td class="px-3"><a href="{{ route('jobs.show', $job) }}" class="fw-semibold text-decoration-none">{{ $job->title }}</a></td>
                    <td>{{ $job->department }}</td>
                    <td>{{ $job->status }}</td>
                    <td>{{ $job->location }}</td>
                </tr>
            @empty
                <tr>
                    <td class="px-3" colspan="4">No jobs yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $jobs->links() }}
</div>
@endsection
