<?php
// PropertyDataTrait.php

namespace App\Traits;

trait FormDataTrait
{
    public function formData($modelClass, $model = null, $specialvalue = null, $defaultData = [])
    {
        
        $fields = $modelClass::$fields;
        $data = ($model) ? [] : null;

        // For create and edit
        foreach ($fields as $field => $label) {
            if (isset($defaultData[$field])) {
                // Set the default value from the controller
                $data[$field] = $defaultData[$field];
            } else {
                // If default data is not provided, set the value from getFieldData
                $data[$field] = $modelClass::getFieldData($field);
            }
            
            $actualvalues = ($model) ? $model : null;  
        }

        return compact('fields', 'data', 'actualvalues','specialvalue','defaultData');
    }

    public function cardData($modelClass, $model = null)
    {
        
        $cards = $modelClass::$card;
        $data = [];

        // For create and edit
        foreach ($cards as $card => $cardtype) {
                // If default data is not provided, set the value from getFieldData
                $data[$card] = $modelClass::getCardData($card, $model);   
        }

        return compact('cards', 'data',);
    }

    public function filterData($modelClass, $model = null)
    {
        
        $filters = $modelClass::$filters;
        $data = [];

        // For create and edit
        foreach ($filters as $filter => $filtername) {
                // If default data is not provided, set the value from getFieldData
                $data[$filter] = $modelClass::getFilterData($filter, $model);   
        }

        return compact('filters', 'data',);
    }

    
}
