<?php

namespace App\Repositories;

use App\Models\Invoice;

class PaymentRepository
{

    public function getInvoicePaymentCount($invoices)
    {
        return $invoices->flatMap(function ($invoice) {
            return $invoice->payments;
        })->count();
    }
    public function getAmountinvoiced($invoices)
    {
        return $invoices->sum('totalamount');
    }

    public function getInvoicepaidAmount($invoices)
    {
        return $invoices->flatMap(function ($invoice) {
            return $invoice->payments;
        })->sum('totalamount');
    }
}