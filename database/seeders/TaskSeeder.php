<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [

            [  
                'name' => 'Automatically Generate Invoice', 
                'command' => 'generate:invoice',
                'job_class' =>'GenerateInvoiceJob',
                'frequency' => 'monthly',
                'variable_one' => '1',
                'variable_two' => '',
                'time' => '13:00:00',
                'status' => '1',
            ],
            
        ];

        foreach ($tasks as $taskData) {
            // Check if a record with the same name, module, and submodule already exists
            $existingTask = Task::where('command', $taskData['command'])
                ->first();

            // Insert only if the record does not exist
            if (!$existingTask) {
                Task::create($taskData);
            }
        }
         
    }
}
