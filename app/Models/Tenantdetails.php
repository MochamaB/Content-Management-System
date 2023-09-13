<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenantdetails extends Model
{
    use HasFactory;
    protected $table = 'tenantdetails';
    protected $fillable = [
        'user_id',
        'user_relationship',
        'emergency_name',
        'emergency_number',
        'emergency_email',

    ];
}
