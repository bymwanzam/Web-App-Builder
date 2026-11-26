<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
            margin: 0;
        }
        .invoice-title {
            font-size: 24px;
            color: #666;
            margin: 10px 0 0 0;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            margin-bottom: 15px;
        }
        .invoice-details {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
        }
        .invoice-details .label {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #3b82f6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            width: 150px;
        }
        .grand-total {
            font-size: 18px;
            color: #3b82f6;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-failed {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="company-name">{{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</h1>
            <p class="invoice-title">INVOICE</p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-column">
                <div class="info-label">Bill To:</div>
                <div class="info-value">
                    <strong>{{ $user->name }}</strong><br>
                    {{ $user->email }}
                </div>
            </div>
            <div class="info-column">
                <div class="invoice-details">
                    <table>
                        <tr>
                            <td class="label">Invoice Number:</td>
                            <td>{{ $transaction->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">Invoice Date:</td>
                            <td>{{ $transaction->transaction_date->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status:</td>
                            <td>
                                @if($transaction->status === 'completed')
                                    <span class="status-badge status-paid">PAID</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="status-badge status-pending">PENDING</span>
                                @else
                                    <span class="status-badge status-failed">FAILED</span>
                                @endif
                            </td>
                        </tr>
                        @if($transaction->payment_method)
                        <tr>
                            <td class="label">Payment Method:</td>
                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right; width: 150px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>
                            @if($plan)
                                {{ $plan->name }} Plan - Monthly Subscription
                            @else
                                {{ ucfirst($transaction->type) }}
                            @endif
                        </strong>
                        @if($subscription)
                            <br>
                            <span style="font-size: 12px; color: #666;">
                                Billing Period: {{ $subscription->created_at->format('F j, Y') }}
                                @if($subscription->renewal_at)
                                    - {{ $subscription->renewal_at->format('F j, Y') }}
                                @endif
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right;">${{ number_format($transaction->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">${{ number_format($transaction->amount, 2) }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">Total:</div>
                <div class="total-value">${{ number_format($transaction->amount, 2) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Thank you for your business!<br>
                This is a computer-generated invoice. No signature required.
            </p>
            <p style="margin-top: 20px;">
                <strong>{{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</strong>
                @if($siteDescription = \App\Models\SystemSetting::get('site_description'))
                    <br>{{ $siteDescription }}
                @endif
            </p>
        </div>
    </div>
</body>
</html>
