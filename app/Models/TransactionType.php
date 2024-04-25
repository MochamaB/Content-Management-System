<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;
    protected $table = 'transaction_types';
    protected $fillable = [
        'name',
        'description',
        'model',
        'account_type',
        'debitaccount_id',
        'creditaccount_id',
    ];

    public static $fields = [
        'model' => ['label' => 'Action of transaction', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'description' => ['label' => 'Transaction Description', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'name' => ['label' => 'Account of Transaction', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'account_type' => ['label' => 'Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'debitaccount_id' => ['label' => 'Debit Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'creditaccount_id' => ['label' => 'Credit Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];

    public static $validation = [
        'name' => 'required',
        'description' => 'required',
        'model' => 'required',
        'account_type' => 'required',
        'debitaccount_id' => 'required',
        'creditaccount_id' => 'required',

    ];

    public static function getFieldData($field)
    {
        switch ($field) {

            case 'name':
                // Retrieve the supervised units' properties
                $name = Chartofaccount::pluck('account_name', 'account_name')->toArray();
                return $name;
            case 'model':
                return [
                    'Invoice' => 'Generate Invoice',
                    'Payment' => 'Make Payment',
                    'Expense' => 'Record Expense',
                    'Deposit' => 'Record Deposit'
                ];
            case 'account_type':
                return [
                    'Asset' => 'Asset',
                    'Liability' => 'Liability',
                    'Income' => 'Income',
                    'Expense' => 'Expense',
                    'Equity' => 'Equity'
                ];

            case 'debitaccount_id':
                $account = Chartofaccount::all();
                $accounts = $account->groupBy('account_type');
                $data = []; // Initialize $data as an empty array
                foreach ($accounts as $category => $item) {
                    $data[$category] = $item->pluck('account_name', 'id')->toArray();
                }
                return $data;
            case 'creditaccount_id':
                $account = Chartofaccount::all();
                $accounts = $account->groupBy('account_type');
                $data = []; // Initialize $data as an empty array
                foreach ($accounts as $category => $item) {
                    $data[$category] = $item->pluck('account_name', 'id')->toArray();
                }
                return $data;
        }
    }

    public function debit()
    {
        return $this->belongsTo(Chartofaccount::class, 'debitaccount_id');
    }

    public function credit()
    {
        return $this->belongsTo(Chartofaccount::class, 'creditaccount_id');
    }
}
