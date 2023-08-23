<?php
// PropertyDataTrait.php

namespace App\Traits;

trait FormDataTrait
{
    public function formData($modelClass, $model = null, $specialvalue = null)
    {
        
        $fields = $modelClass::$fields;
        $data = ($model) ? [] : null;

        // For create and edit
        foreach ($fields as $field => $label) {
            $data[$field] = $modelClass::getFieldData($field);
            
            $actualvalues = ($model) ? $model : null;  
        }

        return compact('fields', 'data', 'actualvalues','specialvalue');
    }

    
}
