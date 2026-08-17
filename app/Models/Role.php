<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];
    protected $fillable = array('name','status', 'label', 'description');

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }
    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function hasPermission(Permission $permission, User $user)
    {
        $permissionRoles = $permission->roles;
        if ($permissionRoles instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            $permissionRoles = $permissionRoles->get();
        }
        return $this->inRole($permissionRoles);
    }
    /**
     * Determine if the role has the given permission.
     *
     * @param  mixed $permission
     * @return boolean
     */
    public function inRole($permission)
    {
        $rolePermissions = $this->permissions;
        if ($rolePermissions instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            $rolePermissions = $rolePermissions->get();
        }

        if (is_string($permission)) {
            return $rolePermissions->contains('name', $permission);
        }
        return !!$permission->intersect($rolePermissions)->count();
    }
}
