<?php

namespace App\Repositories;

use App\Models\Unit;

class UnitRepository
{

    public function getUnitCount($units)
    {
        return $units->count();
    }
    public function getUnitLeasedCount($units)
    {
        return  $units->filter(function ($unit) {
            return $unit->lease !== null;
        })->count();
    }

    public function getInvoicepaidAmount($invoices)
    {
        return $invoices->flatMap(function ($invoice) {
            return $invoice->payments;
        })->sum('totalamount');
    }
    public function getInvoicePaymentCount($invoices)
    {
        return $invoices->flatMap(function ($invoice) {
            return $invoice->payments;
        })->count();
    }
}