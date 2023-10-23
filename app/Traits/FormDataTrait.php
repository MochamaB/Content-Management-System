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

    
}
