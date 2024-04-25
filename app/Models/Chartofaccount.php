<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;

class Chartofaccount extends Model
{
    use HasFactory,FilterableScope;
    protected $table = 'chartofaccounts';
    protected $fillable = [
        'account_number',
        'account_type',
        'account_name',
        'account_level',
    ];

    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'account_type' => ['label' => 'Account Type', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'account_number' => ['label' => 'Account Number', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'account_name' => ['label' => 'Account Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'account_level' => ['label' => 'Account Level', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];

    public static $validation = [
        'account_type' => 'required',
        'account_number' => 'required|unique:chartofaccounts,account_number',
        'account_name' =>  'required|unique:chartofaccounts,account_name',
        'account_level' => 'required',
    ];
    public static function getFieldData($field)
    {
        switch ($field) {
    
            case 'account_type':
                return [
                    'Asset' => 'Asset',
                    'Liability' => 'Liability',
                    'Income' => 'Income',
                    'Expense' => 'Expense',
                    'Equity'=> 'Equity'];

            case 'account_level':
                return [
                    '1' => 'Parent Acount',
                    '2'=> 'Sub Account'];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }
}
