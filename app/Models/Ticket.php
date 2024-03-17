<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'tickets';
    protected $fillable = [
        'property_id',
        'unit_id',
        'chartofaccount_id',
        'subject',
        'category',
        'description',
        'status',
        'priority',
        'raised_by',
        'assigned_type',
        'assigned_id',
        'charged_to',
        'totalamount',
        'duedate',

    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'category' => ['label' => 'Category', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'subject' => ['label' => 'Subject', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'description' => ['label' => 'Description', 'inputType' => 'textarea', 'required' => true, 'readonly' => ''],
        'priority' => ['label' => 'Priority', 'inputType' => 'select', 'required' => true, 'readonly' => ''],




        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'nullable',
        'category' => 'required',
        'subject' => 'required',
        'description' => 'required',
        'priority' => 'required',

    ];

    public static function getFieldData($field)
    {
        switch ($field) {

            case 'category':
                return [
                    'complaint' => 'Complaint',
                    'inquiry' => 'General Inquiry',
                    'maintenance' => 'Maintenance Request',
                    'feedback' => 'Feedback or Suggestion',
                    'other' => 'Other'
                ];
            case 'priority':
                return [
                    'critical' => 'Critical',
                    'high' => 'High',
                    'normal' => 'Normal',
                    'low' => 'Low',
                ];
        }
    }


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function assigned()
    {
        return $this->morphTo();
    }
    public function workorders()
    {
        return $this->hasMany(Workorder::class);
    }
}
