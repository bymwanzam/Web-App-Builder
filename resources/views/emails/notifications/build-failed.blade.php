@extends('emails.layout')

@section('title', 'Build Failed')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Unfortunately, your app build has failed.</p>

<div class="error-box">
    <p><strong>Build Details:</strong></p>
    <p><strong>App:</strong> {{ $build->app->name ?? 'N/A' }}</p>
    <p><strong>Platform:</strong> {{ ucfirst($build->platform) }}</p>
    <p><strong>Version:</strong> {{ $build->version_name }} (Build #{{ $build->version_code }})</p>
    @if($build->error_message)
    <p><strong>Error:</strong> {{ $build->error_message }}</p>
    @endif
</div>

<p class="text-center">
    <a href="{{ route('user.apps.show', $build->app_id) }}" class="btn btn-danger">View Build Details</a>
</p>

<p>Please check your app configuration and try again. Common issues include:</p>
<ul>
    <li>Invalid app icons or splash screens</li>
    <li>Missing required permissions</li>
    <li>Invalid package name format</li>
</ul>

<p>If the problem persists, please contact our support team for assistance.</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
