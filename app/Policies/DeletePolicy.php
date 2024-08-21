<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Amenity;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeletePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function delete($model)
    {
        
        // Property deletion checks
        if ($model instanceof Property) {
            if ($model->units()->count() > 0 || $model->invoices()->count() > 0 || $model->leases()->count() > 0) {
                return false;
            }
        } 
        // Amenity deletion checks
        elseif ($model instanceof Amenity) {
            if ($model->properties()->exists()) {
                return false;
            }
        }
        return true;
    }

}
