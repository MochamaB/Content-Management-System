<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccount;
use App\Models\Expense;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Services\CardService;
use App\Services\ExpenseService;
use App\Models\WebsiteSetting;
use App\Traits\FormDataTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Actions\RecordTransactionAction;
use App\Actions\UploadMediaAction;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     use FormDataTrait;
     protected $controller;
     protected $model;
     private $cardService;
     private $tableViewDataService;
     private $filterService;
     private $expenseService;
     private $recordTransactionAction;
     protected $uploadMediaAction;

     public function __construct(CardService $cardService,TableViewDataService $tableViewDataService,
     FilterService $filterService, RecordTransactionAction $recordTransactionAction, ExpenseService $expenseService)
     {
         $this->model = Expense::class;
         $this->controller = collect([
             '0' => 'expense', // Use a string for the controller name
             '1' => ' Expenses',
         ]);
         $this->cardService = $cardService;
         $this->tableViewDataService = $tableViewDataService;
         $this->filterService = $filterService;
         $this->expenseService = $expenseService;
         $this->recordTransactionAction = $recordTransactionAction;
        
     }

    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPropertyFilters($request);
        $expensedata = $this->model::with('property','unit')->ApplyDateFilters($filters)->get();
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getExpenseData($expensedata,true);
           
           return View('admin.CRUD.form', compact('filterdata', 'tableData', 'controller'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null,$model = null)
    {
        if ($model === 'properties') {
            $property = Property::find($id);
            $unit = $property->units;
        } elseif ($model === 'units') {
            $unit = Unit::find($id);
            $property = Property::where('id', $unit->property->id)->first();
            Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        }
        $account = Chartofaccount::whereIn('account_type', ['Expenses'])->get();
        $accounts = $account->groupBy('account_type');
        $vendors = Vendor::all();


       

        return View('admin.accounting.create_expenses', compact('id', 'property', 'unit', 'accounts','model','vendors'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        ////REFRENCE NO
       
         $unitnumber = $request->unit_id ?? 'N';
        
        //// INSERT DATA TO THE UNITCHARGE
        $validationRules = Expense::$validation;
        $validatedData = $request->validate($validationRules);

        $this->expenseService->generateExpense(null,null,$validatedData,$request);

        $previousUrl = Session::get('previousUrl');
        if ($previousUrl && $unitnumber) {
            return redirect($previousUrl)->with('status', 'Your Expense has been saved successfully');
        } else {
            return redirect('expense/')->with('status', ' Expense Added Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        $pageheadings = collect([
            '0' => $expense->name,
            '1' => $expense->referenceno,
            '2' => $expense->status,
        ]);
        $instance = $expense;

        $property = Property::find($expense->property_id);
        $unit = $property->units;
        $account = Chartofaccount::whereIn('account_type', ['Expenses'])->get();
        $accounts = $account->groupBy('account_type');
        $vendors = Vendor::all();

        return View('admin.accounting.edit_expense', compact('pageheadings','instance','property', 'unit', 'accounts','vendors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        $pageheadings = collect([
            '0' => $expense->name,
            '1' => $expense->referenceno,
            '2' => $expense->status,
        ]);
        $instance = $expense;

        $property = Property::with('units')->find($expense->property_id);
        $unit = $property->units;
        $account = Chartofaccount::whereIn('account_type', ['Expenses'])->get();
        $accounts = $account->groupBy('account_type');
        $vendors = Vendor::all();

        return View('admin.accounting.edit_expense', compact('pageheadings','instance','property', 'unit', 'accounts','vendors'));
        //
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
        //
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
