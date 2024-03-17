<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;

class Workorder extends Model
{
    use HasFactory, InteractsWithMedia, MediaUpload;
    protected $table = 'workorders';
    protected $fillable = [
            'ticket_id',
            'user_id',
            'notes',
    ];

     ////////// FIELDS FOR CREATE AND EDIT METHOD
     public static $fields = [
        'ticket_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'user_id' => ['label' => 'Vendor Category', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'notes' => ['label' => 'Name of Vendor', 'inputType' => 'text', 'required' => true, 'readonly' => ''],

        // Add more fields as needed
    ];

    public static $validation = [
        'ticket_id' => 'required',
        'notes' => 'required',
    ];

    /////Filter options
    public static $filter = [
        'user_id' => 'Staff/Vendor',
        // Add more filter fields as needed
    ];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function tickets()
    {
        return $this->belongsTo(Ticket::class,'ticket_id');
    }
    
}
