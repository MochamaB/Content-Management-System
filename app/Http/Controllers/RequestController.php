<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Session;

class RequestController extends Controller
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

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = ModelsRequest::class;
        $this->controller = collect([
            '0' => 'request', // Use a string for the controller name
            '1' => 'Request',
        ]);
        $this->tableViewDataService = $tableViewDataService;
    }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $request =ModelsRequest::all();
        } else {
            $request =ModelsRequest::all();
          //  $tablevalues = $user->filterUsers();
        }
        $mainfilter =  ModelsRequest::pluck('category')->toArray();
     //   $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getRequestData($request,false);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'),
      //  $filterData,
        [
         //   'cardData' => $cardData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $property = Property::find($id);
        $properties = Property::all();
        $viewData = $this->formData($this->model);

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.maintenance.create_request', compact('id','property','properties'),$viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validationRules = ModelsRequest::$validation;
        $validatedData = $request->validate($validationRules);
        $requestData = new ModelsRequest;
        $requestData->fill($validatedData);
        $requestData->status = 'New';
        $requestData->user_id = $user->id;
        $requestData->save();

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Your request has been sent successfully');
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
