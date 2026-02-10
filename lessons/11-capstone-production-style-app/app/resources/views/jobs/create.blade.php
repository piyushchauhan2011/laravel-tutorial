@extends('layouts.capstone')

@section('title', 'Create Job')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Create Job</h1>
    <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">Back to jobs</a>
</div>

<div class="cap-card">
    <div class="cap-card__body">
@if ($errors->any())
    <ul class="mb-3">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="post" action="{{ route('jobs.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="cap-form__label" for="title">Title</label>
            <input id="title" name="title" value="{{ old('title') }}" required class="form-control">
        </div>
        <div class="col-md-3">
            <label class="cap-form__label" for="department">Department</label>
            <input id="department" name="department" value="{{ old('department') }}" required class="form-control">
        </div>
        <div class="col-md-3">
            <label class="cap-form__label" for="location">Location</label>
            <input id="location" name="location" value="{{ old('location') }}" required class="form-control">
        </div>
        <div class="col-md-4">
            <label class="cap-form__label" for="employment_type">Type</label>
            <select id="employment_type" name="employment_type" class="form-select">
            @foreach (['full_time', 'contract', 'internship'] as $type)
                <option value="{{ $type }}" @selected(old('employment_type', 'full_time') === $type)>{{ $type }}</option>
            @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="cap-form__label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
            @foreach (['draft', 'open', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', 'open') === $status)>{{ $status }}</option>
            @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="cap-form__label d-block">Remote</label>
            <label><input type="checkbox" name="is_remote" value="1" @checked(old('is_remote'))> Remote allowed</label>
        </div>
        <div class="col-md-6">
            <label class="cap-form__label" for="salary_min">Salary min</label>
            <input id="salary_min" name="salary_min" value="{{ old('salary_min') }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="cap-form__label" for="salary_max">Salary max</label>
            <input id="salary_max" name="salary_max" value="{{ old('salary_max') }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="cap-form__label" for="description">Description</label>
            <textarea id="description" name="description" rows="6" class="form-control" required>{{ old('description') }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Create Job</button>
</form>
    </div>
</div>
@endsection
