<?php

namespace App\Http\Controllers;

use App\Http\Services\VolunteerRolesService;
use App\Http\Services\VolunteerService;
use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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
     * @return Collection<int,Volunteer>
     */
    public function index(): Collection
    {
        return $this->volunteerService->getAll();
    }

    /**
     * Store a newly created resource in storage.
     * @return void
     */
    public function store(Request $request): void
    {
        $volunteer = new Volunteer();
        $volunteer->idHumHub = $request->input("idHumHub");
        $volunteer->regime = $request->input("regime");
        $volunteer->castAndSet(
            "extra_attributes",
            $request->input("extra_attributes", []),
        );

        $this->volunteerService->save($volunteer);
    }

    /**
     * Display the specified resource.
     * @return Volunteer
     */
    public function show(Volunteer $volunteer): Volunteer
    {
        return $volunteer;
    }

    /**
     * Update the specified resource in storage.
     * @return void
     */
    public function update(Request $request, Volunteer $volunteer): void
    {
        $volunteer->idHumHub = $request->input(
            "idHumHub",
            $volunteer->idHumHub,
        );
        $volunteer->regime = $request->input("regime", $volunteer->regime);
        $volunteer->extra_attributes = $request->input(
            "extra_attributes",
            $volunteer->extra_attributes,
        );
    }

    /**
     * Remove the specified resource from storage.
     * @return void
     */
    public function destroy(Volunteer $volunteer): void
    {
        $this->volunteerService->delete($volunteer);
    }

    /**
     * Add a role to a volunteer.
     *
     * @param Volunteer $volunteer
     * @param int $roleId
     * @return JsonResponse
     */
    public function addRole(Volunteer $volunteer, int $roleId): JsonResponse
    {
        $role = Role::findOrFail($roleId);
        $result = $this->roleService->addRole($volunteer->id, $roleId);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => sprintf(
                    'Role "%s" has been added to the volunteer
.',
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
     *
     * @param Volunteer $volunteer
     * @param int $roleId
     * @return JsonResponse
     */
    public function removeRole(Volunteer $volunteer, int $roleId): JsonResponse
    {
        $result = $this->roleService->removeRole($volunteer->id, $roleId);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" =>
                    "The role has been removed from the volunteer successfully.",
            ]);
        }

        return response()->json(
            [
                "status" => "not_removed",
                "message" => "Role not found or already removed.",
            ],
            200,
        );
    }

    /**
     * Replace all roles of a volunteer.
     *
     * @param Volunteer $volunteer
     * @param Collection $roleIds
     * @return JsonResponse
     */
    public function replaceRoles(
        Volunteer $volunteer,
        Collection $roleIds,
    ): JsonResponse {
        $result = $this->roleService->replaceRoles($volunteer->id, $roleIds);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => "Roles have been replaced successfully.",
            ]);
        }

        return response()->json(
            [
                "status" => "volunteer_not_found",
                "message" => "Volunteer not found.",
            ],
            404,
        );
    }

    /**
     * Get all roles assigned to a volunteer.
     *
     * @param Volunteer $volunteer
     * @return JsonResponse
     */
    public function getRoles(Volunteer $volunteer): JsonResponse
    {
        $roles = $this->roleService->getVolunteerRoles($volunteer->id);

        return response()->json([
            "status" => "success",
            "roles" => $roles,
        ]);
    }

    /**
     * Check if a volunteer has a specific role.
     *
     * @param Volunteer $volunteer
     * @param int $roleId
     * @return JsonResponse
     */
    public function hasRole(Volunteer $volunteer, int $roleId): JsonResponse
    {
        $result = $this->roleService->hasRole($volunteer->id, $roleId);

        return response()->json([
            "status" => "success",
            "has_role" => $result,
            "role_id" => $roleId,
        ]);
    }

    /**
     * Remove a role by enum or name.
     *
     * @param Volunteer $volunteer
     * @param RolesEnum|Role|string $role
     * @return JsonResponse
     */
    public function removeRoleByName(Volunteer $volunteer, $role): JsonResponse
    {
        $result = $this->roleService->removeRoleByName($volunteer->id, $role);

        if ($result) {
            return response()->json([
                "status" => "success",
                "message" => "Role has been removed successfully.",
            ]);
        }

        return response()->json(
            [
                "status" => "not_removed",
                "message" => "Role not found or already removed.",
            ],
            200,
        );
    }
}
