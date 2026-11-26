@extends('emails.layout')

@section('title', 'Verify Your Email Address')

@section('content')
<p>Hi {{ $user->name ?? 'there' }},</p>

<p>Please click the button below to verify your email address.</p>

<p class="text-center">
    <a href="{{ $url }}" class="btn">Verify Email Address</a>
</p>

<p>Once verified, you'll have full access to all features of your account.</p>

<p>If you did not create an account, no further action is required.</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>

<p class="text-muted mt-4">If you're having trouble clicking the button, copy and paste the URL below into your web browser:<br>{{ $url }}</p>
@endsection
