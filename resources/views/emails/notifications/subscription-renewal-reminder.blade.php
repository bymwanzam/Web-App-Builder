@extends('emails.layout')

@section('title', 'Subscription Renewal Reminder')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>This is a friendly reminder that your subscription will renew soon.</p>

<div class="info-box">
    <p><strong>Subscription Details:</strong></p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    <p><strong>Amount:</strong> {{ $subscription->plan->currency ?? 'USD' }} {{ number_format($subscription->amount, 2) }}</p>
    <p><strong>Renewal Date:</strong> {{ $subscription->renewal_at ? $subscription->renewal_at->format('M j, Y') : 'N/A' }}</p>
</div>

<p>Your payment method will be charged automatically on the renewal date. Make sure your payment information is up to date to avoid any service interruption.</p>

<p class="text-center">
    <a href="{{ route('user.billing') }}" class="btn">Manage Subscription</a>
</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
