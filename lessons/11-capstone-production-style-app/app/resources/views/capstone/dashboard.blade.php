@extends('layouts.capstone')

@section('title', 'Feature Flags')

@section('content')
<div class="cap-card mb-4">
    <div class="cap-card__header">
        <h1 class="h4 mb-1">Capstone Control Center</h1>
        <p class="mb-0 text-muted">Laravel Pennant feature flags for canary-style rollouts.</p>
    </div>
    <div class="cap-card__body">

@php
    $canaryEnabled = collect($flags)->firstWhere('feature', 'canary_release_banner')['enabled'] ?? false;
@endphp

@if ($canaryEnabled)
    <div class="notice notice--info">
        Canary release is active for selected users.
    </div>
@endif

<div class="flag-grid">
    @foreach ($flags as $flag)
        <div class="flag-grid__item">
            <div>
                <div class="flag-grid__name">{{ $flag['label'] }}</div>
                <div class="flag-grid__key">{{ $flag['feature'] }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="cap-pill {{ $flag['enabled'] ? 'cap-pill--enabled' : 'cap-pill--disabled' }}">
                    {{ $flag['enabled'] ? 'enabled' : 'disabled' }}
                </span>
                <form method="post" action="{{ route('capstone.flags.update', $flag['feature']) }}">
                    @csrf
                    <input type="hidden" name="enabled" value="{{ $flag['enabled'] ? 0 : 1 }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ $flag['enabled'] ? 'Disable' : 'Enable' }}</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
    </div>
</div>

<div class="cap-card">
    <div class="cap-card__body">
        <div class="cap-section-title">API Check</div>
        <p class="mb-2">Fetch current flags:</p>
        <pre class="mb-0"><code>GET /api/v1/feature-flags</code></pre>
    </div>
</div>
@endsection
