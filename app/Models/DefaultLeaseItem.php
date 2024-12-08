<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultLeaseItem extends Model
{
    use HasFactory;
    protected $table = 'default_lease_items';
    protected $fillable = ['category', 'item_description'];

    public function leaseItems()
    {
        return $this->hasMany(LeaseItem::class, 'default_item_id', 'id');
    }
}
