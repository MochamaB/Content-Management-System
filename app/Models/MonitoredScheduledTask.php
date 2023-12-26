<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredScheduledTask extends Model
{
    use HasFactory;
    protected $table = 'monitored_scheduled_tasks';
    protected $primaryKey = 'id'; // Update with your actual primary key
}
