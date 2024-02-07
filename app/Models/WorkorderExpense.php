<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkorderExpense extends Model
{
    use HasFactory;
    protected $table = 'workorder_expenses';
    protected $fillable = [
            'request_id',
            'quantity',
            'item',
            'price',
    ];

     ////////// FIELDS FOR CREATE AND EDIT METHOD
     public static $fields = [
        'request_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'user_id' => ['label' => 'Vendor Category', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'notes' => ['label' => 'Name of Vendor', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'email' => ['label' => 'Email', 'inputType' => 'email', 'required' => true, 'readonly' => ''],
        'phonenumber' => ['label' => 'Phonenumber', 'inputType' => 'tel', 'required' => true, 'readonly' => ''],



        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'vendorcategory_id' => 'required',
        'name' => 'required',
        'email' => 'required',
        'phonenumber' => 'required',


    ];

    /////Filter options
    public static $filter = [
        'vendorcategory_id' => 'Type',
        'property_streetname' => 'Location',
        'property_status' => 'Status',
        // Add more filter fields as needed
    ];

}
