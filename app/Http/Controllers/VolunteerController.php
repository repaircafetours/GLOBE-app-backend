<?php

namespace App\Http\Controllers;

use App\Http\Services\VolunteerRolesService;
use App\Http\Services\VolunteerService;
use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Requests\PatchVolunteerRequest;

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
     * The authenticated volunteer (admin) is recorded as the actor in the audit log.
     */
    public function store(StoreVolunteerRequest $request): Volunteer
    {
        $validated = $request->validated();

        $volunteer = new Volunteer();
        $volunteer->idHumHub = $validated["idHumHub"];
        $volunteer->password = $validated["password"];
        $volunteer->login = $validated["login"] ?? null;
        $volunteer->castAndSet(
            "extra_attributes",
            $request->input("extra_attributes", []),
        );

        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->volunteerService->save($volunteer, $actor);

        return $volunteer;
    }

    /**
     * Display the specified resource.
     */
    public function show(Volunteer $volunteer): Volunteer
    {
        return $volunteer;
    }

    /**
     * Update the specified resource in storage.
     * The authenticated volunteer is recorded as the actor in the audit log.
     */
    public function update(
        PatchVolunteerRequest $request,
        Volunteer $volunteer,
    ): void {
        $validated = $request->validated();
        $volunteer->idHumHub = $validated["idHumHub"] ?? $volunteer->idHumHub;
        $volunteer->login = array_key_exists("login", $validated)
            ? $validated["login"]
            : $volunteer->login;
        $volunteer->extra_attributes =
            $validated["extra_attributes"] ?? $volunteer->extra_attributes;

        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->volunteerService->save($volunteer, $actor);
    }

    /**
     * Remove the specified resource from storage.
     * The authenticated volunteer is recorded as the actor in the audit log.
     */
    public function destroy(Request $request, Volunteer $volunteer): void
    {
        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->volunteerService->delete($volunteer, $actor);
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
