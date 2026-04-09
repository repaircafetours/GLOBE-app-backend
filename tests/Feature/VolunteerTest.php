<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerTest extends TestCase
{
    use RefreshDatabase;

    private function makeVolunteer(int $idHumHub): Volunteer
    {
        $volunteer = new Volunteer();
        $volunteer->idHumHub = $idHumHub;
        $volunteer->save();

        return $volunteer;
    }

    private function makeRole(string $name): Role
    {
        $role = new Role();
        $role->name = $name;
        $role->save();

        return $role;
    }

    // GET /api/v1/volunteers
    public function test_index_returns_empty_list_when_no_volunteers(): void
    {
        $response = $this->getJson("/api/v1/volunteers");

        $response->assertOk()->assertJsonCount(0);
    }

    public function test_index_returns_all_volunteers(): void
    {
        $this->makeVolunteer(1);
        $this->makeVolunteer(2);
        $this->makeVolunteer(3);

        $response = $this->getJson("/api/v1/volunteers");

        $response->assertOk()->assertJsonCount(3);
    }

    // POST /api/v1/volunteers
    public function test_store_creates_a_volunteer(): void
    {
        $response = $this->postJson("/api/v1/volunteers", [
            "idHumHub" => 42,
        ]);

        $response->assertCreated()->assertJsonFragment(["idHumHub" => 42]);
        $this->assertDatabaseHas("volunteers", ["idHumHub" => 42]);
    }

    public function test_store_creates_volunteer_with_extra_attributes(): void
    {
        $response = $this->postJson("/api/v1/volunteers", [
            "idHumHub" => 99,
            "extra_attributes" => ["foo" => "bar"],
        ]);

        $response->assertCreated()->assertJsonFragment(["idHumHub" => 99]);
        $this->assertDatabaseHas("volunteers", ["idHumHub" => 99]);
    }

    public function test_store_fails_when_idHumHub_is_missing(): void
    {
        $response = $this->postJson("/api/v1/volunteers", []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["idHumHub"]);
    }

    public function test_store_fails_when_idHumHub_is_not_an_integer(): void
    {
        $response = $this->postJson("/api/v1/volunteers", [
            "idHumHub" => "not-an-integer",
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["idHumHub"]);
    }

    public function test_store_fails_with_duplicate_idHumHub(): void
    {
        $this->makeVolunteer(42);

        $response = $this->postJson("/api/v1/volunteers", [
            "idHumHub" => 42,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["idHumHub"]);
    }

    // GET /api/v1/volunteers/{volunteer}
    public function test_show_returns_the_requested_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(77);

        $response = $this->getJson("/api/v1/volunteers/{$volunteer->id}");

        $response->assertOk()->assertJsonFragment(["idHumHub" => 77]);
    }

    public function test_show_returns_404_for_unknown_volunteer(): void
    {
        $response = $this->getJson("/api/v1/volunteers/99999");

        $response->assertNotFound();
    }

    // PATCH /api/v1/volunteers/{volunteer}
    public function test_update_modifies_the_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(10);

        $response = $this->patchJson("/api/v1/volunteers/{$volunteer->id}", [
            "idHumHub" => 20,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas("volunteers", [
            "id" => $volunteer->id,
            "idHumHub" => 20,
        ]);
    }

    public function test_update_fails_when_idHumHub_is_not_an_integer(): void
    {
        $volunteer = $this->makeVolunteer(10);

        $response = $this->patchJson("/api/v1/volunteers/{$volunteer->id}", [
            "idHumHub" => "bad-value",
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["idHumHub"]);
    }

    public function test_update_returns_404_for_unknown_volunteer(): void
    {
        $response = $this->patchJson("/api/v1/volunteers/99999", [
            "idHumHub" => 20,
        ]);

        $response->assertNotFound();
    }

    // DELETE /api/v1/volunteers/{volunteer}
    public function test_destroy_deletes_the_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(55);

        $response = $this->deleteJson("/api/v1/volunteers/{$volunteer->id}");

        $response->assertOk();
        $this->assertDatabaseMissing("volunteers", ["id" => $volunteer->id]);
    }

    public function test_destroy_returns_404_for_unknown_volunteer(): void
    {
        $response = $this->deleteJson("/api/v1/volunteers/99999");

        $response->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/volunteers/{id}/roles
    // -------------------------------------------------------------------------

    public function test_get_roles_returns_empty_list_for_new_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(1);

        $response = $this->getJson("/api/v1/volunteers/{$volunteer->id}/roles");

        $response
            ->assertOk()
            ->assertJsonFragment(["status" => "success"])
            ->assertJsonFragment(["roles" => []]);
    }

    // POST /api/v1/volunteers/{id}/roles/{role_id}/add
    public function test_add_role_attaches_role_to_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(1);
        $role = $this->makeRole("admin");

        $response = $this->postJson(
            "/api/v1/volunteers/{$volunteer->id}/roles/{$role->id}/add",
        );

        $response->assertOk()->assertJsonFragment(["status" => "success"]);

        $this->assertDatabaseHas("volunteer_roles", [
            "volunteer_id" => $volunteer->id,
            "role_id" => $role->id,
        ]);
    }

    public function test_add_role_twice_returns_conflict(): void
    {
        $volunteer = $this->makeVolunteer(1);
        $role = $this->makeRole("admin");

        $volunteer->roles()->attach($role->id);

        $response = $this->postJson(
            "/api/v1/volunteers/{$volunteer->id}/roles/{$role->id}/add",
        );

        $response
            ->assertConflict()
            ->assertJsonFragment(["status" => "already_exists"]);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/v1/volunteers/{id}/roles/{role_id}
    // -------------------------------------------------------------------------

    public function test_remove_role_detaches_role_from_volunteer(): void
    {
        $volunteer = $this->makeVolunteer(1);
        $role = $this->makeRole("editor");

        $volunteer->roles()->attach($role->id);

        $response = $this->deleteJson(
            "/api/v1/volunteers/{$volunteer->id}/roles/{$role->id}",
        );

        $response->assertOk()->assertJsonFragment(["status" => "success"]);

        $this->assertDatabaseMissing("volunteer_roles", [
            "volunteer_id" => $volunteer->id,
            "role_id" => $role->id,
        ]);
    }

    public function test_remove_role_returns_404_when_role_not_assigned(): void
    {
        $volunteer = $this->makeVolunteer(1);
        $role = $this->makeRole("editor");

        $response = $this->deleteJson(
            "/api/v1/volunteers/{$volunteer->id}/roles/{$role->id}",
        );

        $response
            ->assertNotFound()
            ->assertJsonFragment(["status" => "not_removed"]);
    }

    // PUT /api/v1/volunteers/{id}/roles
    public function test_replace_roles_syncs_all_roles(): void
    {
        $volunteer = $this->makeVolunteer(1);
        $roleA = $this->makeRole("alpha");
        $roleB = $this->makeRole("beta");
        $roleC = $this->makeRole("gamma");

        $volunteer->roles()->attach([$roleA->id, $roleB->id]);

        $response = $this->putJson(
            "/api/v1/volunteers/{$volunteer->id}/roles",
            ["role_ids" => [$roleC->id]],
        );

        $response->assertOk()->assertJsonFragment(["status" => "success"]);

        $this->assertDatabaseHas("volunteer_roles", [
            "volunteer_id" => $volunteer->id,
            "role_id" => $roleC->id,
        ]);
        $this->assertDatabaseMissing("volunteer_roles", [
            "volunteer_id" => $volunteer->id,
            "role_id" => $roleA->id,
        ]);
        $this->assertDatabaseMissing("volunteer_roles", [
            "volunteer_id" => $volunteer->id,
            "role_id" => $roleB->id,
        ]);
    }
}
