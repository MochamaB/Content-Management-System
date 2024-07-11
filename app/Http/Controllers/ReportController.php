<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Services\Reports\FinancialReportService;
use App\Services\Reports\LeaseReportService;

class ReportController extends Controller
{

    private $financialReportService;
    private $leaseReportService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(FinancialReportService $financialReportService,LeaseReportService $leaseReportService)
    {

        $this->financialReportService = $financialReportService;
        $this->leaseReportService = $leaseReportService;
    }
    public function index()
    {
        $reports = Report::all()->groupBy('module');
        // dd($reports);
        return view('admin.Report.index', compact('reports'));
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
    public function show(Request $request,$id)
    {
        $report = Report::findOrFail($id);
        $pageheadings = collect([
            '0' => $report->title,
            '1' => 'Category',
            '2' => $report->module,
        ]);

        // Determine which service to use based on the module
        switch ($report->module) {
            case 'Financial':
                $service =$this->financialReportService;
                break;

            case 'Lease':
                $service = $this->leaseReportService;
                break;
                // Add more cases as needed for other modules
        }

        $columns = $service->getColumns($report->submodule);
        $filterdata = $service->getFilters($report->submodule);
        // Get filters from the request or any other source
        $filters = request()->all();
        $data = $service->getData($report->submodule, $filters);
        // Load the dynamic filter view
     //   $filterView = $this->getFilterView($filterdata);
        //    dd($data);

        return view('admin.Report.show', compact('report', 'columns', 'data','filterdata','filters','pageheadings'));
    }


    protected function getFilterView($filterdata)
    {
        $viewPath = 'admin.Report.report_filter';
        echo "View Path: $viewPath"; // Add this line to echo the view path
    
        return view($viewPath, compact('filterdata'));
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
