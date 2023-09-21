<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;
    protected $table = 'website_settings';
    protected $fillable = [
            'company_name',
            'site_name',
            'company_logo',
            'company_flavicon',
            'company_telephone',
            'company_email',
            'company_location',
            'company_googlemaps',
            'company_aboutus',
            'site_currency',
            'banner_desc',
            
    ];

    
}
