<?php

namespace App\Http\Services;

use App\Models\Role;
use App\Models\Volunteer;
use App\Models\RolesEnum;
use Illuminate\Database\Eloquent\Collection;

class VolunteerRolesService
{
    /**
     * Add a role to a volunteer.
     * Returns true if newly attached, false if already assigned.
     */
    public function addRole(Volunteer $volunteer, Role $role): bool
    {
        $result = $volunteer->roles()->syncWithoutDetaching([$role->id]);

        return !empty($result["attached"]);
    }

    /**
     * Add a role to a volunteer by role name (string or enum).
     * Returns true if successful, false if role not found.
     */
    public function addRoleByName(
        Volunteer $volunteer,
        string|RolesEnum $roleName,
    ): bool {
        $role = match (true) {
            $roleName instanceof RolesEnum => Role::find($roleName->value),
            is_string($roleName) => Role::where("name", $roleName)->first(),
            default => throw new \InvalidArgumentException(
                "Role name must be a string or RolesEnum.",
            ),
        };

        return $role && $this->addRole($volunteer, $role);
    }

    /**
     * Remove a role from a volunteer.
     * Returns true if role was removed, false if it was not assigned.
     */
    public function removeRole(Volunteer $volunteer, Role $role): bool
    {
        return (bool) $volunteer->roles()->detach($role->id);
    }

    /**
     * Add multiple roles to a volunteer at once.
     * Returns the sync result array (attached/detached/updated).
     */
    public function addMultipleRoles(
        Volunteer $volunteer,
        Collection $roles,
    ): array {
        return $volunteer
            ->roles()
            ->syncWithoutDetaching($roles->pluck("id")->all());
    }

    /**
     * Remove multiple roles from a volunteer at once.
     * Returns the number of roles removed.
     */
    public function removeMultipleRoles(
        Volunteer $volunteer,
        Collection $roles,
    ): int {
        return $volunteer->roles()->detach($roles->pluck("id")->all());
    }

    /**
     * Replace all roles of a volunteer.
     */
    public function replaceRoles(Volunteer $volunteer, Collection $roles): void
    {
        $volunteer->roles()->sync($roles->pluck("id")->all());
    }

    /**
     * Check if a volunteer has a specific role.
     * Uses the already-loaded relationship to avoid an extra query.
     */
    public function hasRole(Volunteer $volunteer, Role $role): bool
    {
        return $volunteer->roles->contains("id", $role->id);
    }

    /**
     * Check if a volunteer has a role via ID, enum, or model.
     */
    public function hasRoleBySpecified(
        Volunteer $volunteer,
        RolesEnum|Role|int|string $role,
    ): bool {
        $roleId = match (true) {
            $role instanceof RolesEnum => $role->value,
            $role instanceof Role => $role->id,
            is_int($role) || is_string($role) => $role,
            default => throw new \InvalidArgumentException(
                "Unsupported role type.",
            ),
        };

        return $volunteer->roles->contains("id", $roleId);
    }

    /**
     * Remove a role by name (enum, model, or string).
     */
    public function removeRoleByName(
        Volunteer $volunteer,
        RolesEnum|Role|string $role,
    ): bool {
        $roleObject = match (true) {
            $role instanceof Role => $role,
            $role instanceof RolesEnum => Role::find($role->value),
            is_string($role) => Role::where("name", $role)->first(),
            default => throw new \InvalidArgumentException(
                "Role identifier must be an enum, model, or string.",
            ),
        };

        return $roleObject && $this->removeRole($volunteer, $roleObject);
    }
}
