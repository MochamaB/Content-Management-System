<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;

class TransactionTypeController extends Controller
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
        $this->model = TransactionType::class;

        $this->controller = collect([
            '0' => 'transaction-type', // Use a string for the controller name
            '1' => ' Transaction Type',
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = TransactionType::all();
        } else {
            $tablevalues = TransactionType::all();
        }

        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['NAME','MODEL', 'DEBIT ACCOUNT','CREDIT ACCOUNT','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->name,
                $item->model,
                $item->debit->account_name,
                $item->credit->account_name,

            ];
        }

        return View('admin.CRUD.form',compact('tableData', 'controller'),$viewData,
            ['controller' => $this->controller]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewData = $this->formData($this->model);

        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = TransactionType::$validation;
        $validatedData = $request->validate($validationRules);
        $transactionType = new TransactionType;
        $transactionType->fill($validatedData);
        $transactionType->save();

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
    public function edit(TransactionType $transactionType)
    {
        ///// Used to Set Chartof account name in the edit view.///
        $specialvalue = collect([
            'debitaccount_id' => $transactionType->debit->account_name, // Use a string for the controller name
            'creditaccount_id' => $transactionType->credit->account_name,
            '1' => ' Transactiontype',
        ]);
        $viewData = $this->formData($this->model,$transactionType,$specialvalue);
        
        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = TransactionType::find($id);
        // Get the list of fillable fields from the model
        $model->update($request->all());
        return redirect($this->controller[0])->with('status', $this->controller[1] . ' was edited Successfully');     
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
