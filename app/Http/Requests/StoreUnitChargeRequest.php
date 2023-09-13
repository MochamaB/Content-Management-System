<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_id' => 'required', 
            'unit_id' => 'required|numeric', 
            'chartofaccounts_id' => 'required', 
            'charge_name' => 'required',
            'charge_cycle' => 'required',
            'charge_type' => 'required',
            'rate' => 'required',
            'parent_utility' => 'required',
            'recurring_charge' => 'nullable|',
            'startdate' => 'nullable|date',
            'nextdate' => 'nullable|date', 
        ];
    }
}
