<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    protected $table = 'testimonials';
    protected $fillable = [
            'client_name',
            'client_title',
            'client_company',
            'client_picture',
            'testimonial',
    ];
    public static $fields = [
        'client_name' => ['label' => 'Client Name', 'inputType' => 'text','required' =>true,'readonly' => true],
        'client_title' => ['label' => 'Job/Designation', 'inputType' => 'text','required' =>true, 'readonly' => ''],
        'client_company' => ['label' => 'Company', 'inputType' => 'text', 'required' =>false,'readonly' => ''],
        'client_picture' => ['label' => 'Picture', 'inputType' => 'picture', 'required' =>false,'readonly' => ''],
        'testimonial' => ['label' => 'Information', 'inputType' => 'textarea', 'required' =>true,'readonly' => ''],
       
      
        // Add more fields as needed
    ];
    public static $validation = [
        'client_name' => 'required|max:255',
        'client_title' => 'required|max:255',
        // Add more validation rules for other fields
    ];
    
 
    public static function getFieldData($field)
    {
    switch ($field) {
        case 'slider_title':
            return Slider::pluck('slider_title')->toArray();
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }
    public static function getValidationRules($field)
    {
        // Define validation rules for the specified field
        if ($field === 'client_name') {
            return [
                'client_name' => 'required|string|max:255',
            ];
        } elseif ($field === 'client_title') {
            return [
                'client_title' => 'required|email|unique:your_table,email',
            ];
        }

        return []; // Default empty rules array
    }
}
