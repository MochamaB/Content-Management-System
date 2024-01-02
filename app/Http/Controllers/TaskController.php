<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\DB;
use App\Services\TableViewDataService;

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
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Task::class;
        $this->controller = collect([
            '0' => 'task', // Use a string for the controller name
            '1' => 'New Task',
        ]);
        $this->tableViewDataService = $tableViewDataService;
    }

    public function getTaskData($taskdata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['NAME', 'COMMAND', 'FREQUENCY', 'DATES', 'TIMES', 'STATUS', 'MONITOR', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($taskdata as $item) {
            $status = $item->status ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
            $monitoredTasks = $item->monitoredTasks;
            if ($monitoredTasks->isEmpty()) {
                $monitorLink = '<form method="POST" action="' . url('linkmonitor', ['task' => $item]) . '" style="display:inline;">'
                                    . csrf_field() .
                                    '<button type="submit" class="badge badge-warning" style="float: right; margin-right:10px">Link Monitor</button>
                                </form>';
                $monitorLink = '<a href="' . url('linkmonitor', ['task' => $item]) . '" class="badge badge-warning" style="float: right; margin-right:10px">Add Monitor</a>';
            } else {
                $monitorLink = '<span class="badge badge-success" style="float: right; margin-right:10px">Monitoring</a>';
            }

            //  dd($monitoredTasks);
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->name,
                $item->command,
                $item->frequency,
                'Day of the month: ' . $item->variable_one . ' </br> Day of the month: ' . $item->variable_two,
                $item->time,
                $status,
                $monitorLink,
            ];
        }

        return $tableData;
    }

    public function getTaskMonitorData($taskMonitorData)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['', 'NAME', 'TYPE', 'STARTED AT', 'FINISHED AT', 'FAILED AT', 'SKIPPED AT', 'CREATED_AT'],
            'rows' => [],
        ];

        foreach ($taskMonitorData as $item) {

            $tableData['rows'][] = [
                'id' => $item->id,
                'name' => $item->name,  // Replace 'name' with the actual property you want to display
                'type' => $item->type,
                $item->last_started_at,
                $item->last_finished_at,
                $item->last_failed_at,
                $item->last_skipped_at,
                $item->created_at, // Replace 'type' with the actual property you want to display
                // Add other properties as needed
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

    public function linkmonitor(Task $task)
    {
       // dd($task);
        DB::table('monitored_scheduled_tasks')
            ->where('name', $task->command)
            ->update(['task_id' => $task->id]);

        return redirect($this->controller['0'])->with('status', 'Monitor for this Task Added Successfully');
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
        $task = new Task;
        $task->fill($validatedData);
        $task->save();

        // After saving the task, find matching records in monitored_scheduled_tasks and update them
        DB::table('monitored_scheduled_tasks')
            ->where('name', $task->command)
            ->update(['task_id' => $task->id]);

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $pageheadings = collect([
            '0' => $task->name,
            '1' => $task->command,
            '2' => '',
        ]);

        //   $monitoredTasks = $task->monitoredTasks;
        //   dd($monitoredTasks);

        /// DATA FOR MONITORED TAB
        $taskMonitorData = DB::table('monitored_scheduled_tasks')
            ->where('name', $task->command)
            ->get();
        // dd($payments);
        $taskMonitorTableData = $this->getTaskMonitorData($taskMonitorData);

        //s   dd($taskMonitorTableData);


        $tabTitles = collect([
            'Monitor Task',
        ]);



        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Monitor Task') {
                $tabContents[] = View('admin.task.task_monitor', ['data' => $taskMonitorTableData], compact('taskMonitorData'))->render();
            }
        }
        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $viewData = $this->formData($this->model, $task);

        return View('admin.CRUD.form', $viewData);
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
