@extends('emails.layout')

@section('title', 'Subscription Cancelled')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Your subscription has been cancelled as requested.</p>

<div class="info-box">
    <p><strong>Subscription Details:</strong></p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    @if($subscription->ends_at)
    <p><strong>Access Until:</strong> {{ $subscription->ends_at->format('M j, Y') }}</p>
    @endif
</div>

<p>You will continue to have access to your current plan features until the end of your billing period. After that, your account will be downgraded to the free plan.</p>

<p>We're sorry to see you go! If you change your mind, you can reactivate your subscription at any time.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">Reactivate Subscription</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
