<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaseItem extends Model
{
    use HasFactory;
    protected $table = 'lease_items';
    protected $fillable = [
        'lease_id',
        'default_item_id',
        'condition',
        'cost',
        

    ];
   
    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function defaultItem()
    {
        return $this->belongsTo(DefaultLeaseItem::class, 'default_item_id');
    }
   
}
