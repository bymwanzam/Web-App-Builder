@extends('emails.layout')

@section('title', 'Subscription Expired')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Your subscription has expired.</p>

<div class="warning-box">
    <p><strong>Subscription Details:</strong></p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    <p><strong>Expired On:</strong> {{ $subscription->ends_at ? $subscription->ends_at->format('M j, Y') : now()->format('M j, Y') }}</p>
</div>

<p>Your account has been downgraded to the free plan. To continue enjoying premium features and build credits, please renew your subscription.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">Renew Subscription</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
