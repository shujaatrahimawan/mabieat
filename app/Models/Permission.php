<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = ['id'];
    protected $fillable = array('name', 'label', 'description');

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    /**
     * Determine if the permission belongs to the role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function inRole($role)
    {
        $permissionRoles = $this->roles;
        if ($permissionRoles instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            $permissionRoles = $permissionRoles->get();
        }

        if (is_string($role)) {
            return $permissionRoles->contains('name', $role);
        }
        return !!$role->intersect($permissionRoles)->count();
    }
}
