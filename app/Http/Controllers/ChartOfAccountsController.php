<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;

class ChartOfAccountsController extends Controller
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
        $this->model = Chartofaccounts::class; 
        
        $this->controller = collect([
            '0' => 'chartofaccounts', // Use a string for the controller name
            '1' => 'New Chart of accounts',
        ]);
    }


    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues= Chartofaccounts::all();
        }else{
            $tablevalues = Chartofaccounts::all();
        }

        
        $mainfilter =  $this->model::pluck('account_type')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['ACCOUNT NAME', 'ACCOUNT TYPE','ACCOUNT NUMBER','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->account_name,
                $item->account_type,
                $item->account_number,
              
            ];
        }

        return View('admin.CRUD.form',compact('mainfilter','tableData','controller'),$viewData, 
        ['controller' => $this->controller]);
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
        if (Chartofaccounts::where('account_number', $request->account_number)
        ->exists()) {
            return back()->with('status','Account already exists.');
        }
       
        $model = new Chartofaccounts();
        // Get the list of fillable fields from the model
        $fillableFields = $model->getFillable();
        // Loop through the fillable fields and set the values from the request
        foreach ($fillableFields as $field) {
            // Make sure the field exists in the request before setting it
            if ($request->has($field)) {
                $model->$field = $request->input($field);
            }
        }
        $model->save();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Chartofaccounts $chartofaccounts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Chartofaccounts $chartofaccounts)
    {
        dd($chartofaccounts);
         $viewData = $this->formData($this->model,$chartofaccounts);


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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chartofaccounts $chartofaccounts)
    {
        //
    }
}
