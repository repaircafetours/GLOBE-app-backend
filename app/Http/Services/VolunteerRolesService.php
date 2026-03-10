<?php

namespace App\Http\Services;

use App\Models\Role;
use App\Models\Volunteer;
use App\Models\RolesEnum;
use Illuminate\Database\Eloquent\Collection;

class VolunteerRolesService
{
    /**
     * Add a role to a volunteer
     * Returns true if successful, false if already assigned.
     */
    public function addRole(int $volunteerId, int $roleId): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return false;
        }

        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        return !$volunteer->roles()->where('role_id', $roleId)->exists()
            && $volunteer->roles()->attach($roleId);
    }

    /**
        * Add a role to a volunteer by role name (string or enum)
        * Returns true if successful, false if role not found.
        */
    public function addRoleByName(int $volunteerId, string|RolesEnum $roleName): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return false;
        }

        $roleId = match (true) {
            $roleName instanceof RolesEnum => $roleName->value,
            is_string($roleName) => Role::where('name', $roleName)->value('id') ?? throw new \InvalidArgumentException("Role not found by name."),
            default => throw new \InvalidArgumentException("Role name must be a string or RolesEnum."),
        };

        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        return !$volunteer->roles()->where('role_id', $roleId)->exists()
            && $volunteer->roles()->attach($roleId);
    }

    /**
     * Remove a role from a volunteer
     * Returns true if role was removed, false if role didn't exist.
     */
    public function removeRole(int $volunteerId, int $roleId): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return false;
        }

        if (!$volunteer->roles()->where('role_id', $roleId)->exists()) {
            return false;
        }

        return $volunteer->roles()->detach($roleId);
    }

    /**
     * Add multiple roles to a volunteer
     * Returns array of success/failure for each role.
     */
    public function addMultipleRoles(int $volunteerId, Collection $roleIds): array
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return ['volunteer_not_found' => true];
        }

        $results = [];
        foreach ($roleIds as $roleId) {
            $results[] = $this->addRole($volunteerId, $roleId);
        }

        return ['results' => $results];
    }

    /**
     * Remove multiple roles from a volunteer
     * Returns array of success/failure for each role.
     */
    public function removeMultipleRoles(int $volunteerId, Collection $roleIds): array
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return ['volunteer_not_found' => true];
        }

        $results = [];
        foreach ($roleIds as $roleId) {
            $results[] = $this->removeRole($volunteerId, $roleId);
        }

        return ['results' => $results];
    }

    /**
     * Replace all roles of a volunteer
     * Returns true if operation started, false on volunteer not found.
     */
    public function replaceRoles(int $volunteerId, Collection $roleIds): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return false;
        }

        $currentRoleIds = collect($volunteer->roles)
            ->map(function ($relation) {
                return $relation['relation']->first()?->role_id;
            })
            ->filter()
            ->map('intval')
            ->all();

        $toRemove = collect($currentRoleIds)
            ->diff($roleIds->map('intval')->all())
            ->all();

        foreach ($toRemove as $roleId) {
            $this->removeRole($volunteerId, $roleId);
        }

        foreach ($roleIds as $roleId) {
            $this->addRole($volunteerId, $roleId);
        }

        return true;
    }

    /**
     * Get all roles for a volunteer
     */
    public function getVolunteerRoles(int $volunteerId): ?Collection
    {
        $volunteer = Volunteer::find($
volunteerId);
        return $volunteer ? $volunteer->roles() : null;
    }

    /**
     * Check if a volunteer has a specific role by ID
     */
    public function hasRole(int $volunteerId, int $roleId): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        return $volunteer && $volunteer->roles()->where('role_id', $roleId)->exists();
    }

    /**
     * Check if a volunteer has a role via ID, enum, or model
     */
    public function hasRoleBySpecified(int $volunteerId, RolesEnum|Role|int|string $role): bool
    {
        $volunteer = Volunteer::find($volunteerId);
        if (!$volunteer) {
            return false;
        }

        $roleId = match (true) {
            $role instanceof RolesEnum => $role->value,
            $role instanceof Role => $role->id,
            is_int($role) => $role,
            is_string($role) => $role,
            default => throw new \InvalidArgumentException("Unsupported role type."),
        };

        return $volunteer->roles()->where('role_id', $roleId)->exists();
    }

    /**
     * Remove a role by name (enum or string)
     */
    public function removeRoleByName(int $volunteerId, RolesEnum|Role|string $role): bool
    {
        $roleId = match (true) {
            $role instanceof RolesEnum => $role->value,
            $role instanceof Role => $role->id,
            is_string($role) => $role,
            is_int($role) => $role,
            default => throw new \InvalidArgumentException("Role identifier must be an enum, model, string, or int."),
        };

        return $this->removeRole($volunteerId, $roleId);
    }
}
