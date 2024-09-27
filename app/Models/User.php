<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as Roles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;


/**
 * @method filterUsers()
 */
class User extends Authenticatable implements HasMedia, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phonenumber',
        'idnumber',
        'password',
        'status',
        'profilepicture'
    ];

    public static $fields = [
        'firstname' => ['label' => 'First Name', 'inputType' => 'text', 'required' => true, 'readonly' => true],
        'lastname' => ['label' => 'Last Name', 'inputType' => 'text', 'required' => true, 'readonly' => true],
        'email' => ['label' => 'Email Address', 'inputType' => 'email', 'required' => true, 'readonly' => ''],
        'phonenumber' => ['label' => 'phone Number', 'inputType' => 'tel', 'required' => true, 'readonly' => ''],
        'idnumber' => ['label' => 'ID Number', 'inputType' => 'number', 'required' => true, 'readonly' => true],
        'password' => ['label' => 'Password', 'inputType' => 'password', 'required' => true, 'readonly' => ''],
        'confirm_password' => ['label' => 'Confirm Password', 'inputType' => 'password', 'required' => true, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'profilepicture' => ['label' => 'Profile Picture', 'inputType' => 'picture', 'required' => false, 'readonly' => ''],


        // Add more fields as needed
    ];
    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                return Property::pluck('property_name', 'id')->toArray();
            case 'status':
                return [
                    'active' => 'Active',
                    'deactivated', 'Deactivated'
                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }

    public static $filters = [
        'property' => ['label' => 'Property', 'inputType' => 'select'],
        'Unit' => ['label' => 'Unit', 'inputType' => 'select'],
        'role' => ['label' => 'Role', 'inputType' => 'select'],
        'status' => ['label' => 'Status', 'inputType' => 'select'],


        // Add more fields as needed
    ];
    public static function getFilterData($filter)
    {
        $invoice = Invoice::with('property', 'unit', 'model')->get();
        switch ($filter) {
            case 'property':
                $properties = Invoice::with('property')->get()->pluck('property.property_name')->unique()->values()->toArray();
                //    $properties = Property::pluck('property_name')->toArray();
                return $properties;
            case 'Unit':
                $units = Invoice::with('unit')->get()->pluck('unit.unit_number')->unique()->values()->toArray();
                return $units;
            case 'role':
                $roles = Roles::pluck('name')->toArray();
                return  $roles;
            case 'status':
                return [
                    'Active',
                    'Suspended',
                ];
        }
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //   protected $attributes = [
    //       'password' => 'property123', // Replace 'default_password_value' with your desired default password
    //  ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }
    

    /**
     * The units that belong to the user.
     */

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'unit_user', 'user_id', 'property_id')
            //  ->withPivot('property_id')
            ->withTimestamps();
    }
    //// Relationship between user and Unit through pivot
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_user', 'user_id', 'unit_id')
            ->withPivot('property_id')
            ->withTimestamps();
    }
    public function lease()
    {
        return $this->hasOne(Lease::class, 'user_id');
    }

    public function tickets()
    {
        return $this->hasMany(ticket::class, 'user_id');
    }

    public function unitswithoutlease()
    {
        return $this->belongsToMany(Unit::class, 'unit_user', 'user_id', 'unit_id')
            ->leftJoin('leases', 'units.id', '=', 'leases.unit_id')
            ->whereNull('leases.id')
            ->withTimestamps();
    }

    ///// Returning units that are in the unit_user pivot, Has additional data such as properties and leasedata 
    public function supervisedUnits()
    {
        return $this->belongsToMany(Unit::class, 'unit_user', 'user_id', 'unit_id')
            ->with('property', 'lease')
            ->withTimestamps();
    }
    ///// Group properties that are in the unit_user pivot to avoid duplicates in view//////// 

    public function assignedunits()
    {
        $units = $this->supervisedUnits;
        return $units->groupBy('property_id')->map(function ($propertyUnits) {
            return $propertyUnits;
        });
    }

    ////scopes////
    protected $allowedStatuses = ['active', 'draft', 'suspended'];
    public function scopeWithoutActiveLease($query, $role)
    {
        return $query->role($role)
            ->whereNotIn('id', function ($subquery) {
                $subquery->select('user_id')
                    ->from('leases')
                    ->whereIn('status', $this->allowedStatuses);
            });
    }

    public function scopeWithLowerPermissions($query)
    {

        $loggedInUser = Auth::user(); // Assuming you're using this inside a controller or middleware

        $loggedInUserRoles = $loggedInUser->roles;
        $loggedInUserPermissions = $loggedInUserRoles->flatMap(function ($role) {
            return $role->permissions;
        });
        // Retrieve all users with their roles and associated permissions
        $allUsers = User::with('roles.permissions')->get();

        return $query->where('id', '=', $loggedInUser->id)
            ->whereHas('roles.permissions', function ($subquery) use ($loggedInUserPermissions) {
                $subquery->groupBy('id')
                    ->selectRaw('COUNT(*) as permission_count, id')
                    ->having('permission_count', '<', $loggedInUserPermissions->count());
            });
    }
    public function scopeUserAccess($query)
    {
        $user = auth()->user();
        if ($user  && $user->id) {
            // Get the IDs of units assigned to the logged-in user
            $unitIds = $user->units->pluck('id')->toArray();

            return $query->whereHas('units', function ($query) use ($unitIds) {
                $query->whereIn('unit_id', $unitIds);
            });
        }
    }

    public function filterUsers()
    {
        // Check if the user's ID is 1 and return false

        $loggedInUserRoles = $this->roles;
        $loggedInUserPermissions = $loggedInUserRoles->flatMap(function ($role) {
            return $role->permissions;
        });

        // Apply the UserAcess scope to the query
        $query = self::query(); // Initialize the query builder

        if (auth()->user()->id !== 1) {
            $query->userAccess(); // Apply the scope conditionally
        }

        $allUsers = $query
            ->with('roles.permissions')
            ->where('id', '!=', 1) // Exclude users with id 1
            ->get();
        //  dd($allUsers);


        $filteredUsers = $allUsers->filter(function ($user) use ($loggedInUserPermissions) {
            foreach ($user->roles as $role) {
                $rolePermissions = $role->permissions;

                if ($rolePermissions->count() >= $loggedInUserPermissions->count()) {
                    return false;
                }
            }
            return true;
        });

        return $filteredUsers;
    }

    public function scopeApplyFilterUsers($query)
    {
        $user = auth()->user();
    
        // If no user is authenticated or if the user is super admin or an admin, return all users
        if (!$user || $user->id === 1 || stripos($user->roles->first()->name, 'admin') !== false) {
            return $query->where('id', '!=', 1)
            ->where('id', '<>', $user->id); // Return the unfiltered query
        }
    
        $loggedInUserRoles = $user->roles;
        $loggedInUserPermissions = $loggedInUserRoles->flatMap(function ($role) {
            return $role->permissions;
        });
    
        // Apply the UserAccess scope if the user's ID is not 1
        if ($user->id !== 1) {
            $query->userAccess(); // Make sure this is spelled correctly (Access, not Acess)
        }
    
        // Ensure roles and permissions are properly joined and aggregated
        return $query->where('id', '!=', 1) // Exclude users with id 1
            ->where('id', '<>', $user->id) // Exclude loggedinuser from
            ->with('roles.permissions')
            ->whereHas('roles', function ($roleQuery) use ($loggedInUserPermissions) {
                $roleQuery->whereHas('permissions', function ($permissionQuery) use ($loggedInUserPermissions) {
                    // Check for the permission count within roles
                    $permissionQuery->groupBy('permissions.id')
                        ->havingRaw('COUNT(*) < ?', [$loggedInUserPermissions->count()]);
                });
            });
    }
    

    public function scopeTenants(Builder $query, User $user)
    {
        return $query->role('Tenant')
            ->whereHas('units', function (Builder $query) use ($user) {
                $query->whereIn('units.id', $user->units->pluck('id'));
            });
    }
    //// Polymorphism with Invoices Model
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'model');
    }
    
    // Africas Talking Config

    public function routeNotificationForAfricasTalking($notification)
    {
        return $this->phonenumber;
    }

    public function modelrequests()
    {
        return $this->morphMany(Ticket::class, 'assigned');
    }
    public function scopeSameUnit($query, $user)
    {
        return $query->where('id', '<>', 1) // Exclude user with ID 1
            ->where('id', '<>', $user->id) // Exclude logged in user
            ->whereHas('units', function ($query) use ($user) {
                $query->whereIn('unit_id', $user->units->pluck('id')->toArray());
            });
    }
}
