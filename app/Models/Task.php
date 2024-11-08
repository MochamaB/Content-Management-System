<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = [
        'name',
        'command',
        'job_class',
        'frequency',
        'variable_one',
        'variable_two',
        'time',
        'status',
    ];

    public static $fields = [
        'name' => ['label' => 'Task Name', 'inputType' => 'text', 'required' => true, 'readonly' =>''],
        'command' => ['label' => 'Command', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'job_class' => ['label' => 'Job Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'frequency' => ['label' => 'Frequency', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'variable_one' => ['label' => 'Day of Month', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        'variable_two' => ['label' => 'Day of the Month', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        'time' => ['label' => 'Time', 'inputType' => 'time', 'required' => false, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];
    public static $validation = [
        'name' => 'required',
        'command' => 'required',
        'job_class' => 'nullable',
        'frequency' => 'required',
        'variable_one' => 'nullable|numeric',
        'variable_two' => 'nullable|numeric',
        'time' => 'required',
        'status' => 'required|numeric',

    ];
    public static function getFieldData($field)
    {
        switch ($field) {
            case 'frequency':
                // Retrieve the supervised units' properties
                return [
                    'monthly' => 'Monthly',
                    'twiceMonthly' => 'Twice Monthly',
                    'daily' => 'Daily',
                    'dailyAt' => 'Daily At'
                ];
                //  return Property::pluck('property_name','id')->toArray();
            case 'status':
                return [
                    '0' => 'Suspended',
                    '1' => 'Active'
                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }
    
    public function monitoredTasks()
    {
        return $this->hasMany(MonitoredScheduledTask::class, 'task_id', 'id');
    }
    public function getFullJobClassName()
    {
        // Append namespace to job class name
        return "App\\Jobs\\{$this->job_class}";
    }

    public function jobs()
    {
        $fullJobClassName = $this->getFullJobClassName();
        
        return DB::table('jobs')
            ->where(function ($query) use ($fullJobClassName) {
                $query->whereJsonContains('payload->displayName', $fullJobClassName)
                    ->orWhereJsonContains('payload->commandName', $fullJobClassName);
            })
            ->orderBy('created_at', 'desc');
    }
    public function failedJobs()
    {
        $fullJobClassName = $this->getFullJobClassName();
        
        return DB::table('failed_jobs')
            ->where(function ($query) use ($fullJobClassName) {
                $query->whereJsonContains('payload->displayName', $fullJobClassName)
                    ->orWhereJsonContains('payload->commandName', $fullJobClassName);
            })
            ->orderBy('failed_at', 'desc');
    }



}
