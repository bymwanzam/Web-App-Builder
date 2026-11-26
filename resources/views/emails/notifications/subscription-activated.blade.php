@extends('emails.layout')

@section('title', 'Subscription Activated')

@section('content')
<p>Hi {{ $user->name }},</p>

<p>Your subscription has been activated successfully!</p>

<div class="success-box">
    <p><strong>Subscription Details:</strong></p>
    <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
    <p><strong>Amount:</strong> {{ $subscription->plan->currency ?? 'USD' }} {{ number_format($subscription->amount, 2) }}</p>
    <p><strong>Build Credits:</strong> {{ $subscription->plan->monthly_build_credits ?? 0 }} per month</p>
    <p><strong>Next Billing:</strong> {{ $subscription->renewal_at ? $subscription->renewal_at->format('M j, Y') : 'N/A' }}</p>
</div>

<p>Your build credits have been added to your account. You're all set to start creating amazing mobile apps!</p>

<p class="text-center">
    <a href="{{ route('user.apps') }}" class="btn btn-success">Go to Dashboard</a>
</p>

<p>Thank you for subscribing!</p>

<p>Best regards,<br>The {{ $appName ?? config('app.name') }} Team</p>
@endsection
