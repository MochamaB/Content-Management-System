<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Traits\FormDataTrait;

class TaskController extends Controller
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
        $this->model = Task::class;
        $this->controller = collect([
            '0' => 'task', // Use a string for the controller name
            '1' => 'New Task',
        ]);
       
    }

     public function getTaskData($taskdata)
     {
         /// TABLE DATA ///////////////////////////
         $tableData = [
             'headers' => ['NAME','COMMAND', 'FREQUENCY', 'DATES', 'TIMES','STATUS','ACTIONS'],
             'rows' => [],
         ];
 
         foreach ($taskdata as $item) {
             $leaseStatus = $item->lease ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">No Lease</span>';
             $tableData['rows'][] = [
                 'id' => $item->id,
                 $item->name,
                 $item->command,
                 $item->frequency,
                 $item->variable_one.' - '.$item->variable_two,
                 $item->time,
                 $item->status,
             ];
         }
 
         return $tableData;
     }
    public function index()
    {
        $taskdata = Task::all();
        $mainfilter =  Task::distinct()->pluck('name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->getTaskData($taskdata);
        
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
        $validationRules = Task::$validation;
        $validatedData = $request->validate($validationRules);
        $unit = new Task;
        $unit->fill($validatedData);
        $unit->save();

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
    public function edit($id)
    {
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
