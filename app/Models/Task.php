<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = [
        'name',
        'command',
        'frequency',
        'variable_one',
        'variable_two',
        'time',
        'status',
    ];

    public static $fields = [
        'name' => ['label' => 'Task Name', 'inputType' => 'text', 'required' => true, 'readonly' =>''],
        'command' => ['label' => 'Command', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'frequency' => ['label' => 'Frequency', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'variable_one' => ['label' => 'Day of Month', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        'variable_two' => ['label' => 'Day of the Month', 'inputType' => 'text', 'required' => false, 'readonly' => true],
        'time' => ['label' => 'Time', 'inputType' => 'time', 'required' => false, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];
    public static $validation = [
        'name' => 'required',
        'command' => 'required',
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
}
