<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password', 'phone', 'statut', 'avatar', 'role_id', 'is_all_warehouses', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role_id' => 'integer',
        'statut' => 'integer',
        'is_all_warehouses' => 'integer',
    ];

    public function oauthAccessToken()
    {
        return $this->hasMany('\App\Models\OauthAccessToken');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function hasRole($role)
    {
        // Resolve the user's roles as a real Collection. The `roles` property may
        // be either an already-loaded Collection or a BelongsToMany relation
        // instance depending on whether it was touched earlier in the request.
        // Calling ->intersect() on the relation object (instead of a Collection)
        // is what caused intermittent 403 "Authorization denied" responses.
        $userRoles = $this->roles;
        if ($userRoles instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            $userRoles = $userRoles->get();
        }

        if (is_string($role)) {
            return $userRoles->contains('name', $role);
        }
        return !!$role->intersect($userRoles)->count();
    }

    public function assignedWarehouses()
    {
        return $this->belongsToMany('App\Models\Warehouse');
    }

}
