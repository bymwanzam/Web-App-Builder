<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * View invoice in browser.
     */
    public function view(Transaction $transaction)
    {
        // Ensure user owns this transaction
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = $this->generatePdf($transaction);

        return $pdf->stream('invoice-'.$transaction->invoice_number.'.pdf');
    }

    /**
     * Generate PDF from transaction.
     */
    protected function generatePdf(Transaction $transaction)
    {
        $transaction->load(['user', 'subscription.plan']);

        $data = [
            'transaction' => $transaction,
            'user' => $transaction->user,
            'subscription' => $transaction->subscription,
            'plan' => $transaction->subscription?->plan,
            'date' => now()->format('F j, Y'),
        ];

        return Pdf::loadView('invoices.invoice-pdf', $data);
    }
}
