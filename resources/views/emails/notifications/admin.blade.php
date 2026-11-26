@extends('emails.layout')

@section('title', $title ?? 'Admin Notification')

@section('content')
<p>Hello Admin,</p>

<p>{{ $introLine ?? 'An event has occurred that requires your attention.' }}</p>

@if(isset($user))
<div class="info-box">
    <p><strong>User Details:</strong></p>
    <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
    <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
</div>
@endif

@if(isset($detailsBox))
<div class="{{ $boxClass ?? 'info-box' }}">
    @foreach($detailsBox as $label => $value)
    <p><strong>{{ $label }}:</strong> {{ $value }}</p>
    @endforeach
</div>
@endif

@if(isset($actionUrl) && isset($actionText))
<p class="text-center">
    <a href="{{ $actionUrl }}" class="btn {{ $buttonClass ?? '' }}">{{ $actionText }}</a>
</p>
@endif

<p>Best regards,<br>{{ $appName ?? config('app.name') }}</p>
@endsection
