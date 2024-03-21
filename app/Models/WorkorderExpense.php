<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkorderExpense extends Model
{
    use HasFactory;
    protected $table = 'workorder_expenses';
    protected $fillable = [
            'ticket_id',
            'quantity',
            'item',
            'price',
            'amount'
    ];



    public static $validation = [
        'ticket_id' => 'required',
        'quantity' => 'required',
        'item' => 'required',
        'price' => 'required',
        'amount' => 'required',
    ];

    

}
