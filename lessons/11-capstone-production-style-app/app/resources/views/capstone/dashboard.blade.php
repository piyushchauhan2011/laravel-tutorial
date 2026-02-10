<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Capstone Control Center</title>
</head>
<body style="font-family: sans-serif; max-width: 920px; margin: 2rem auto;">
<h1>Capstone Control Center</h1>
<p>Lesson 11 now uses Laravel Pennant feature flags for canary-style rollouts.</p>
<p><a href="{{ route('jobs.index') }}">Open hiring jobs workspace</a></p>

@if (session('status'))
    <p style="padding: 0.75rem; border: 1px solid #ddd;">{{ session('status') }}</p>
@endif

@php
    $canaryEnabled = collect($flags)->firstWhere('feature', 'canary_release_banner')['enabled'] ?? false;
@endphp

@if ($canaryEnabled)
    <p style="padding: 0.75rem; border: 1px solid #0077cc; background: #eef7ff;">
        Canary release is active for selected users.
    </p>
@endif

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Feature</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($flags as $flag)
        <tr>
            <td>
                <strong>{{ $flag['label'] }}</strong><br>
                <small>{{ $flag['feature'] }}</small>
            </td>
            <td>{{ $flag['enabled'] ? 'enabled' : 'disabled' }}</td>
            <td>
                <form method="post" action="{{ route('capstone.flags.update', $flag['feature']) }}">
                    @csrf
                    <input type="hidden" name="enabled" value="{{ $flag['enabled'] ? 0 : 1 }}">
                    <button type="submit">{{ $flag['enabled'] ? 'Disable' : 'Enable' }}</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2 style="margin-top: 2rem;">API Check</h2>
<p>Fetch current flags:</p>
<pre><code>GET /api/v1/feature-flags</code></pre>
</body>
</html>
