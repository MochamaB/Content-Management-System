<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrudFormRequest extends FormRequest
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
         $modelName =$this->input('model_name'); // Assuming 'model' is the route parameter name
        // Or, $modelName = $this->query('model'); // If it's passed as a query parameter

        if (empty($modelName) || !class_exists($modelName)) {
            return [];
        }

        return $modelName::$validation ?? [];

       
    }
}
