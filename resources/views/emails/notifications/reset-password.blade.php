@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
<p>Hi {{ $user->name ?? 'there' }},</p>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p class="text-center">
    <a href="{{ $url }}" class="btn">Reset Password</a>
</p>

<p class="text-muted">This password reset link will expire in {{ $expireMinutes ?? 60 }} minutes.</p>

<p>If you did not request a password reset, no further action is required. Your account is safe.</p>

<div class="info-box">
    <p><strong>Security Tip:</strong> Never share your password with anyone. Our team will never ask for your password.</p>
</div>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>

<p class="text-muted mt-4">If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>{{ $url }}</p>
@endsection
