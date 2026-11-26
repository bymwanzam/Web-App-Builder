@extends('emails.layout')

@section('title', 'Build Completed Successfully')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Great news! Your app build has been completed successfully.</p>

<div class="success-box">
    <p><strong>Build Details:</strong></p>
    <p><strong>App:</strong> {{ $build->app->name ?? 'N/A' }}</p>
    <p><strong>Platform:</strong> {{ ucfirst($build->platform) }}</p>
    <p><strong>Version:</strong> {{ $build->version_name }} (Build #{{ $build->version_code }})</p>
    <p><strong>Format:</strong> {{ strtoupper($build->build_format ?? 'APK') }}</p>
    @if($build->build_duration)
    <p><strong>Build Time:</strong> {{ gmdate('i:s', $build->build_duration) }}</p>
    @endif
</div>

<p class="text-center">
    <a href="{{ route('user.apps.show', $build->app_id) }}" class="btn btn-success">Download Build</a>
</p>

<p>Your build is ready to download. You can also scan the QR code from your dashboard to install directly on your device.</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
