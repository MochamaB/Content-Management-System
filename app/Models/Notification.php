<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
    'id',
    'type', 
    'notifiable_type', 
    'notifiable_id', 
    'data',
    'status'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
