<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    //// Units loggedin user can see

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'user_unit', 'user_id', 'unit_id')
            ->withTimestamps();
    }

    public function unitswithoutlease()
    {
        return $this->belongsToMany(Unit::class, 'user_unit', 'user_id', 'unit_id')
        ->leftJoin('leases', 'units.id', '=', 'leases.unit_id')
        ->whereNull('leases.id')
        ->withTimestamps();
    }
    


    ///// Returning units that are in the user_unit pivot, Has additional data such as properties and leasedata 
    public function supervisedUnits()
    {
        return $this->belongsToMany(Unit::class, 'user_unit', 'user_id', 'unit_id')
            ->with('property','lease')
            ->withTimestamps();
    }
     ///// Group properties that are in the user_unit pivot to avoid duplicates in view//////// 

    public function assignedunits()
    {
        $units = $this->supervisedUnits;
        return $units->groupBy('property_id')->map(function ($propertyUnits) {
            return $propertyUnits;
        });
    }

    

    ////////////Return units that have leases that a logged in user can see

    
    

    //////// Gate to check if user is superAdmin //////// 
    ////scope////
    public function scopeWithoutActiveLease($query, $role)
    {
        return $query->role($role)
            ->whereNotIn('id', function ($subquery) {
                $subquery->select('user_id')
                    ->from('leases')
                    ->where('status', 'active');
            });
        }
    
        public function lease()
        {
            return $this->hasOne(Lease::class, 'user_id');
        }
    

    
}
