<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes, SoftDeleteScope;
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

    public static function getSettingForModel($modelType, $modelId, $key)
    {
        // First, try to get the setting for the specific model (override)
        $overrideSetting = self::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('key', $key)
            ->first();

        // If there's an override, return its value
        if ($overrideSetting) {
            return $overrideSetting->value;
        }
        // If no override, fallback to the global setting (model_id is null)
        $globalSetting = self::where('model_type', $modelType)
        ->whereNull('model_id')
        ->where('key', $key)
        ->first();

        // Return the global setting value or null if no setting is found
        return $globalSetting ? $globalSetting->value : null;
    }
}
