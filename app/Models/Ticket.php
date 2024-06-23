<?php

namespace App\Models;

use Carbon\Carbon;
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
                    'Complaint' => 'Complaint',
                    'Inquiry' => 'General Inquiry',
                    'Maintenance' => 'Maintenance Request',
                    'Feedback' => 'Feedback or Suggestion',
                    'Other' => 'Other'
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

    public function scopeApplyFilters($query, $filters)
    {
        
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                    $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
                } else {
                    // Use where on the other columns
                    $query->where($column, $value);
                }
            }
        }
       // Add default filter for the last two months
       if (empty($filters['from_date']) && empty($filters['to_date'])) {
        $query->where("created_at", ">", Carbon::now()->subMonths(4));
    }

        return $query;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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
    public function workorderExpenses()
    {
        return $this->hasMany(WorkorderExpense::class, 'ticket_id');
    }
    public function getItems()
    {
        return $this->hasMany(WorkorderExpense::class, 'ticket_id');
    }
}
