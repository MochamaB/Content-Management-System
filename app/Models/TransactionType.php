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
        'model',
        'debitaccount_id',
        'creditaccount_id',
    ];

    public static $fields = [
        'name' => ['label' => 'Transaction Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'model' => ['label' => 'Model', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'debitaccount_id' => ['label' => 'Debit Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'creditaccount_id' => ['label' => 'Credit Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];

    public static $validation = [
        'name' => 'required',
        'model' => 'required',
        'debitaccount_id' => 'required',
        'creditaccount_id' => 'required',

    ];

    public static function getFieldData($field)
    {
        switch ($field) {

            case 'model':
                return [
                    'Invoice' => 'Invoice',
                    'Payment' => 'Payment',
                    'Expense' => 'Expense',
                    'Deposit' => 'Deposit'
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
