<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PaymentMethodConfig;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use App\Services\TableViewDataService;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;


    public function __construct(TableViewDataService $tableViewDataService,)
    {
        $this->model = PaymentMethod::class;
        $this->controller = collect([
            '0' => 'payment-method', // Use a string for the controller name
            '1' => ' Payment Method',
        ]);
        $this->tableViewDataService = $tableViewDataService;
    }
    public function getPaymentMethodData($PaymentMethoddata)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PROPERTY','NAME', 'TYPE','ACTIONS'],
            'rows' => [],
        ];

        foreach ($PaymentMethoddata as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property->property_name,
                $item->name,
                $item->type,
               
            ];
        }

        return $tableData;
    }
    public function index()
    {
        $PaymentMethoddata = $this->model::all();
        $mainfilter =  $this->model::distinct()->pluck('name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getPaymentMethodData($PaymentMethoddata, true);

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null, $model = null)
    {
        if ($model === 'properties') {
            $property = Property::find($id);

        } else {
            $property = Property::all();
        }
      

        return View('admin.Accounting.create_paymentmethod',compact('property','model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Data Entry validation. Allow only one payment name e.,g M-PESA/////////////
        if (PaymentMethod::where('name', $request->name)
            ->where('property_id', $request->property_id)
            ->exists()
        ) {
            return redirect()->back()->with('statuserror', 'Payment Type for the property already in system.');
        }
        $validationRules = PaymentMethod::$validation;
        $validatedData = $request->validate($validationRules);
        $PaymentMethod = new PaymentMethod;
        $PaymentMethod->fill($validatedData);
        $PaymentMethod->is_active = 1;
        $PaymentMethod->save();

          // Create the PaymentMethodConfig if applicable
          if ($request->name === 'bank' || $request->name === 'm-pesa') {
            PaymentMethodConfig::create([
                'payment_method_id' => $PaymentMethod->id,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'mpesa_shortcode' => $request->mpesa_shortcode,
                'mpesa_account_number' => $request->mpesa_account_number,
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'passkey' => $request->passkey,
            ]);
        }

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethod $PaymentMethod)
    {
       
        $pageheadings = collect([
            '0' => $PaymentMethod->name,
            '1' => $PaymentMethod->account_name,
            '2' => $PaymentMethod->account_number,
        ]);
        $paymentMethod = PaymentMethod::findOrFail($PaymentMethod->id);
        $paymentMethodConfig = PaymentMethodConfig::where('payment_method_id', $PaymentMethod->id)->first();
        return view('admin.Accounting.edit_paymentmethod', compact('paymentMethod', 'paymentMethodConfig'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethod $PaymentMethod,Request $request)
    {
        $request->validate(PaymentMethod::$validation);

        // Fill and update the PaymentMethod
        $PaymentMethod->fill($request->all());
        $PaymentMethod->is_active = $request->has('is_active') ? $request->is_active : true;
        $PaymentMethod->update();

         // Update or create the PaymentMethodConfig if applicable
         if ($request->name === 'bank' || $request->name === 'm-pesa') {
            $paymentMethodConfig = PaymentMethodConfig::firstOrNew(['payment_method_id' => $PaymentMethod->id]);
            $paymentMethodConfig->fill($request->all());
            $paymentMethodConfig->save();
        } 

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
