@extends('emails.layout')

@section('title', 'Welcome to ' . ($appName ?? config('app.name')))

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Welcome to <strong>{{ $appName ?? config('app.name') }}</strong>! We're excited to have you on board.</p>

<p>Your account has been created successfully and you're now ready to start building amazing mobile apps for your websites and platforms.</p>

<div class="info-box">
    <p><strong>Getting Started:</strong></p>
    <p>1. Create your first app from the dashboard</p>
    <p>2. Configure your app settings and design</p>
    <p>3. Build and download your native mobile app</p>
</div>

<p class="text-center">
    <a href="{{ route('user.apps') }}" class="btn">Go to Dashboard</a>
</p>

<p>If you have any questions, feel free to reach out to our support team.</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
