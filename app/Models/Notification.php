<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['type', 'notifiable_type', 'notifiable_id', 'data'];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
