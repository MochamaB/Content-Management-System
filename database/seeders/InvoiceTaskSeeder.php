<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $task = Task::updateOrCreate([
            'name' => 'Automatically Generate Invoice', 
            'command' => 'generate:invoice',
            'frequency' => 'monthly',
            'variable_one' => '1',
            'variable_two' => '',
            'time' => '13:00:00',
            'status' => '1',
        ]);

         
    }
}
