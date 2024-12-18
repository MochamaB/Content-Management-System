<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class UnitDetail extends Model
{
    use HasFactory;
    protected $table = 'unit_details';
    protected $fillable = [
            'unit_id',
            'user_id',
            'title',
            'description',
            'size',
            'slug',
            'feautured',
            'amenities',
            'additional_features',
            
    ];
    protected $casts = [
        'featured' => 'boolean',
    ];

    public static $fields = [
        'unit_id' => ['label' => 'Unit Number', 'inputType' => 'select','required' =>true,'readonly' => true],
        'unit_property' => ['label' => 'Unit Property', 'inputType' => 'select','required' =>true, 'readonly' => ''],
        'slug' => ['label' => 'Slug', 'inputType' => 'text', 'required' =>false,'readonly' => ''],
        'desc' => ['label' => 'Description', 'inputType' => 'text', 'required' =>false,'readonly' => ''],
      
        // Add more fields as needed
    ];

    public static function getFieldData($field)
    {
    switch ($field) {
        case 'unit_id':
            // Retrieve the supervised units' properties
      
                if (Gate::allows('view-all', auth()->user())) {
                    $units = Unit::pluck('unit_number','id')->toArray();
                   
                } else {
                    $units = auth()->user()->supervisedUnits->pluck('unit_number', 'id')->toArray();
                }         
                return $units;
          //  return Property::pluck('property_name','id')->toArray();
            case 'unit_property':
                return [
                    'mainphoto' => 'Main Photo',
                    'photo', 'Photo',
                    'video' => 'Video',
                    'feature', 'Feature'
                ];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
