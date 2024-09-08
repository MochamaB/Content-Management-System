<?php

// app/Services/DepositService.php

namespace App\Services;


use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Actions\RecordTransactionAction;
use App\Models\Deposit;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\DepositItems;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{
    private $calculateTotalAmountAction;
    private $recordTransactionAction;


    public function __construct(
        CalculateInvoiceTotalAmountAction $calculateTotalAmountAction,
        RecordTransactionAction $recordTransactionAction
    ) {
        $this->recordTransactionAction = $recordTransactionAction;
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
    }

    public function generateDeposit(Model $model = null, User $user = null, $validatedData = null)
    {


        $DepositData = $this->getDepositHeaderData($model, $user, $validatedData);

        //1. Create Deposit Header Data

        $deposit = $this->createDeposit($DepositData);

        //2. Create Deposit items
        $this->createDepositItems($deposit, $model, $validatedData);

        //3. Update Total Amount in Payment Header
        $this->calculateTotalAmountAction->total($deposit);

        //4. Create Transactions for ledger


        $this->recordTransactionAction->transaction($deposit, $model);

        //  $this->recordTransactionAction->voucherCharges($Deposit, $model);

        return $deposit;
    }




    //////2. GET DATA FOR VOUCHER HEADER DATA
    private function getDepositHeaderData($model, $user, $validatedData)
    {
        if (!is_null($validatedData)) {
            return [
                'property_id' => $validatedData['property_id'],
                'unit_id' => $validatedData['unit_id'] ?? null,
                'unitcharge_id' => null,
                'model_type' => $validatedData['model_type'],
                'model_id' => $validatedData['model_id'],
                'name' => $validatedData['name'], ///Generated from securitydeposit
                'totalamount' => null,
                'status' => 'unpaid',
                'duedate' => $validatedData['duedate'] ?? null,
            ];
        } else {
            $doc = 'DEP-';
            $propertynumber = 'P' . str_pad($model->property_id, 2, '0', STR_PAD_LEFT);
            $unitnumber = $model->unit_id ?? 'N';
            $date = Carbon::now()->format('ymd');
            $referenceno = $doc . $propertynumber . $unitnumber . '-' . $date;
            $usermodelname = get_class($user);

            return [
                'property_id' => $model->property_id,
                'unit_id' => $model->unit_id,
                'unitcharge_id' => $model->unit_id ?? null,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
                'name' => $model->charge_name, ///Generated from securitydeposit
                'totalamount' => null,
                'status' => 'unpaid',
                'duedate' => null,
            ];
        }
    }

    private function createDeposit($data)
    {
        return Deposit::create($data);
    }

    private function createDepositItems($deposit, $model, $validatedData)
    {

        if (!is_null($validatedData)) {
            // Create deposit items from the form input names
            foreach ($validatedData['chartofaccount_id'] as $index => $item) {
                $depositItem = new DepositItems([
                    'deposit_id' => $deposit->id,
                    'unitcharge_id' => null,
                    'chartofaccount_id' => $item ?? null,
                    'description' => $validatedData['description'][$index] ?? null,
                    'amount' => $validatedData['amount'][$index] ?? null,
                ]);
                $depositItem->save();
            }
        } else {
            // Get items from the model (e.g., invoices)
            // $items = $model->getItems();

            // Create deposit items from the model items

            $depositItem = new DepositItems([
                'deposit_id' => $deposit->id,
                'unitcharge_id' => $model->unitcharge_id ?? $model->id,
                'chartofaccount_id' => $model->chartofaccounts_id,
                'description' => $model->charge_name,
                'amount' => $model->rate,
            ]);
            $depositItem->save();
        }
    }




    ////// PAyment voucher from the createmethod
    public function generateDepositForm($validatedData, $request, $referenceno)
    {
        $deposit = new Deposit();
        $deposit->fill($validatedData);
        $deposit->referenceno = $referenceno;
        $deposit->save();

        //4. Create Transactions for ledger
        $this->recordTransactionAction->transaction($deposit);
    }

    public function updateDeposit($depositId, $validatedData, $user)
    {
        DB::beginTransaction();

        try {
            $deposit = Deposit::findOrFail($depositId);

            // Update deposit header
            $headerData = $this->getDepositHeaderData(null, $user, $validatedData);
            $deposit->update($headerData);

            // Update or create deposit items
            $this->updateDepositItems($deposit, $validatedData);

            //3. Update Total Amount in Payment Header
            $this->calculateTotalAmountAction->total($deposit);

            // Update associated transaction
            $this->recordTransactionAction->updateTransaction($deposit);

        // Update associated ledger entries
            $this->recordTransactionAction->updateLedgerEntries($deposit);

            DB::commit();
            return $deposit;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating deposit: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateDepositItems($deposit, $validatedData)
    {
        // Get all current deposit items
        $currentItems = $deposit->getItems()->get();
        
        // Keep track of updated and new item IDs
        $updatedItemIds = [];
    
        foreach ($validatedData['chartofaccount_id'] as $index => $chartOfAccountId) {
            $itemData = [
                'chartofaccount_id' => $chartOfAccountId,
                'description' => $validatedData['description'][$index] ?? null,
                'amount' => $validatedData['amount'][$index] ?? null,
            ];
    
            // Check if there's a matching existing item
            $existingItem = $currentItems->first(function ($item) use ($chartOfAccountId, $itemData) {
                return $item->chartofaccount_id == $chartOfAccountId &&
                       $item->description == $itemData['description'];
            });
    
            if ($existingItem) {
                // Update existing item
                $existingItem->update($itemData);
                $updatedItemIds[] = $existingItem->id;
            } else {
                // Create new item
                $newItem = $deposit->getItems()->create($itemData);
                $updatedItemIds[] = $newItem->id;
            }
        }
    
        // Delete items that weren't updated or created
        $deposit->getItems()->whereNotIn('id', $updatedItemIds)->delete();
    
        // Refresh the deposit relationship
        $deposit->load('getItems');
    }
}
