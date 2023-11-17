<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\UpdateNextDateAction;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $invoiceService;
    private $updateNextDateAction;

    public function __construct(InvoiceService $invoiceService, UpdateNextDateAction $updateNextDateAction)
    {
        $this->invoiceService = $invoiceService;
        $this->updateNextDateAction = $updateNextDateAction;
    }

    public function getInvoiceData($invoicedata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['INVOICE DATE','TYPE', 'TENANT', 'STATUS', 'AMOUNT DUE', 'AMOUNT PAID'],
            'rows' => [],
        ];

        foreach ($invoicedata as $item) {
            $invoiceStatus = $item->lease ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">No Lease</span>';
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->created_at,
                $item->invoice_type.''.$item->referenceno,
                $item->firstname.' - '.$item->lastname,
                $item->firstname,
                $item->totalamount,
            ];
        }

        return $tableData;
    }

    public function index()
    {
        $unitCharges = Unitcharge::where('recurring_charge', 'yes')
            // $unitCharges = Unitcharge::where('charge_name', 'rent')
            //  ->where('nextdate', '<=', now()) // Check nextdate for generating invoices
            ->get();
        foreach ($unitCharges as $item) {
            $items = $item;
        }
        $children = $unitCharges->children;
        //   $parent = $unitCharges->parent;
        dd($children);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View('admin.lease.invoice');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


    }
    public function generateInvoice(Request $request)
    {
        ///1. GET UNITS WITH RECURRING CHARGE
        $unitcharges = Unitcharge::where('recurring_charge', 'yes')
                    ->where('parent_id',null)    
                    ->get();

        foreach ($unitcharges as $unitcharge) {
                //1. Create invoice items from invoice service app/Services/InvoiceService
                $this->invoiceService->generateInvoice($unitcharge);

               //2. Update the nextdate in the unitcharge based on charge_cycle logic
                $this->updateNextDateAction->invoicenextdate($unitcharge);

                //3. Update Duedate in invoice 
          
        }
        return redirect()->back()->with('status', 'Sucess Invoice generated.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
