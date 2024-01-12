<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;

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


    public function __construct()
    {
        $this->model = PaymentMethod::class;
        $this->controller = collect([
            '0' => 'payment-method', // Use a string for the controller name
            '1' => ' Payment Method',
        ]);
    }
    public function getPaymentMethodData($PaymentMethoddata)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['NAME', 'ACCOUNT NAME', 'ACCOUNT NUMBER', 'PROVIDER', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($PaymentMethoddata as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->name,
                $item->account_name,
                $item->account_number,
                $item->provider,
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
        $tableData = $this->getPaymentMethodData($PaymentMethoddata);

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewData = $this->formData($this->model);

        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Data Entry validation/////////////
        if (PaymentMethod::where('name', $request->name)
            ->where('account_number', $request->account_number)
            ->exists()
        ) {
            return redirect()->back()->with('statuserror', 'Payment method Already in system.');
        }
        $validationRules = PaymentMethod::$validation;
        $validatedData = $request->validate($validationRules);
        $PaymentMethod = new PaymentMethod;
        $PaymentMethod->fill($validatedData);
        $PaymentMethod->save();

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
        $viewData = $this->formData($this->model, $PaymentMethod);
        $pageheadings = collect([
            '0' => $PaymentMethod->name,
            '1' => $PaymentMethod->account_name,
            '2' => $PaymentMethod->account_number,
        ]);

        return View('admin.CRUD.form', compact('pageheadings'), $viewData);
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
        $validationRules = PaymentMethod::$validation;
        $validatedData = $request->validate($validationRules);
        $PaymentMethod->fill($validatedData);
        $PaymentMethod->update();

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
