<?php

namespace App\Http\Controllers;

use App\Http\Services\VolunteerRolesService;
use App\Http\Services\VolunteerService;
use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    private VolunteerService $volunteerService;
    private VolunteerRolesService $roleService;

    public function __construct(
        VolunteerService $volunteerService,
        VolunteerRolesService $roleService,
    ) {
        $this->volunteerService = $volunteerService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Volunteer::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Volunteer::create($request->all());;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $volunteer_id)
    {
        return Volunteer::find($volunteer_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Volunteer $volunteer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Volunteer $volunteer): void
    {
        $this->volunteerService->delete($volunteer);
    }

    /**
     * Add a role to a volunteer.
     */
    public function addRole(Volunteer $volunteer, Role $role): JsonResponse
    {
        $result = $this->roleService->addRole($volunteer, $role);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => sprintf(
                    'Role "%s" has been added to the volunteer.',
                    $role->name,
                ),
            ]);
        }

        return response()->json(
            [
                "status" => "already_exists",
                "message" => sprintf(
                    'Role "%s" is already assigned to this volunteer.',
                    $role->name,
                ),
            ],
            409,
        );
    }

    /**
     * Remove a role from a volunteer.
     */
    public function removeRole(Volunteer $volunteer, Role $role): JsonResponse
    {
        $result = $this->roleService->removeRole($volunteer, $role);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => sprintf(
                    'Role "%s" has been removed from the volunteer successfully.',
                    $role->name,
                ),
            ]);
        }

        return response()->json(
            [
                "status" => "not_removed",
                "message" => "Role not found or already removed.",
            ],
            404,
        );
    }

    /**
     * Replace all roles of a volunteer.
     * Expects a JSON body with a "role_ids" array.
     */
    public function replaceRoles(
        Request $request,
        Volunteer $volunteer,
    ): JsonResponse {
        $roles = Role::whereIn("id", $request->input("role_ids", []))->get();

        $this->roleService->replaceRoles($volunteer, $roles);

        return response()->json([
            "status" => "success",
            "message" => "Roles have been replaced successfully.",
        ]);
    }

    /**
     * Get all roles assigned to a volunteer.
     */
    public function getRoles(Volunteer $volunteer): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "roles" => $volunteer->roles,
        ]);
    }

    /**
     * Check if a volunteer has a specific role.
     */
    public function hasRole(Volunteer $volunteer, Role $role): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "has_role" => $this->roleService->hasRole($volunteer, $role),
            "role_id" => $role->id,
            "role_name" => $role->name,
        ]);
    }
}
