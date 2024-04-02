<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    protected $fillable = [
        'model_type',
        'model_id',
        'info',
        'name',
        'key',
        'value',
        'description',
    ];
     ////////// FIELDS FOR CREATE AND EDIT METHOD
     public static $fields = [
        'settingable_type' => ['label' => 'Property Type', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => true],
        'settingable_id' => ['label' => 'Property Name', 'inputType' => 'text', 'required' => true, 'readonly' => true],
        'setting_name' => ['label' => 'Location', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'setting_value' => ['label' => 'Street Address', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        // Add more fields as needed
    ];

    public static $validation = [
        'module' => 'required',
        'name' => 'required',
        'model_type' => 'required',
        'model_id' => 'required',
        'key' => 'required',
        'value' => 'required',
        'description' => 'required',

    ];


  

    public function model()
    {
        return $this->morphTo();
    }
}
