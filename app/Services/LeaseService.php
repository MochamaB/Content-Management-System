<?php

// app/Services/expenseService.php

namespace App\Services;


use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\RecordTransactionAction;
use App\Models\Expense;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\ExpenseItems;
use App\Models\Property;
use App\Models\User;
use App\Actions\UploadMediaAction;

class LeaseService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;
    protected $uploadMediaAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction,
        UploadMediaAction $uploadMediaAction
    ) {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
        $this->uploadMediaAction = $uploadMediaAction;
    }
}