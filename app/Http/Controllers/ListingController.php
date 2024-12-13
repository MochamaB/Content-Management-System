<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitDetail;
use Illuminate\Http\Request;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Services\CardService;


class ListingController extends Controller
{
    protected $controller;
    protected $model;
    private $cardService;
    private $tableViewDataService;
    private $filterService;

    public function __construct(CardService $cardService,TableViewDataService $tableViewDataService,
    FilterService $filterService)
     {
         $this->model = UnitDetail::class;
         $this->controller = collect([
             '0' => 'listing', // Use a string for the controller name
             '1' => ' Listings',
         ]);
         $this->cardService = $cardService;
         $this->tableViewDataService = $tableViewDataService;
         $this->filterService = $filterService;
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getExpenseFilters($request);
        $baseQuery = Unit::with('unitdetails')->ApplyDateFilters($filters);
        $cardData = $this->cardService->expenseCard($baseQuery->get());
        // Variable to track the applied scope
        $tabTitles = ['Units Listed','Units Not Listed'];

        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'Units Listed':
                    $query->has('unitdetails');
                    break;
                case 'Units Not Listed':
                    $query->doesntHave('unitdetails');
                    break;
                    // 'All' doesn't need any additional filters
            }
            $listings = $query->get();
            $count = $listings->count();
            $tableData = $this->tableViewDataService->getUnitListingData($listings, true);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
        
       
      
           
           return View('admin.CRUD.form', compact('tabTitles', 'tabContents','tabCounts','filterdata', 'controller','cardData','filters',));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        //
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
