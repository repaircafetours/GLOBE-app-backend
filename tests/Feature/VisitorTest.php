<?php

namespace Tests\Feature;

use App\Http\Services\Logs\VisitorLoggerService;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Setup – mock the logger to avoid MySQL-specific information_schema queries
    // -------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(VisitorLoggerService::class, function ($mock) {
            $mock->shouldReceive('initialize')->andReturn(null);
            $mock->shouldReceive('log')->andReturn(null);
            $mock->shouldReceive('logDelete')->andReturn(null);
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'email'        => 'john.doe@example.com',
            'title'        => 'Mr',
            'name'         => 'John',
            'surname'      => 'Doe',
            'zip_code'     => '75000',
            'city'         => 'Paris',
            'phone_number' => '0612345678',
            'source'       => 'web',
            'notification' => false,
        ], $overrides);
    }

    private function makeVisitor(array $overrides = []): Visitor
    {
        $visitor = new Visitor();
        foreach ($this->validPayload($overrides) as $key => $value) {
            $visitor->{$key} = $value;
        }
        $visitor->save();

        return $visitor;
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/visitors
    // -------------------------------------------------------------------------

    public function test_index_returns_empty_list_when_no_visitors(): void
    {
        $response = $this->getJson('/api/v1/visitors');

        $response->assertOk()->assertJsonCount(0);
    }

    public function test_index_returns_all_visitors(): void
    {
        $this->makeVisitor(['email' => 'alice@example.com']);
        $this->makeVisitor(['email' => 'bob@example.com']);

        $response = $this->getJson('/api/v1/visitors');

        $response->assertOk()->assertJsonCount(2);
    }

    // -------------------------------------------------------------------------
    // POST /api/v1/visitors
    // -------------------------------------------------------------------------

    public function test_store_creates_a_visitor(): void
    {
        $response = $this->postJson('/api/v1/visitors', $this->validPayload());

        $response->assertOk();
        $this->assertDatabaseHas('visitors', [
            'email'   => 'john.doe@example.com',
            'name'    => 'John',
            'surname' => 'Doe',
        ]);
    }

    public function test_store_fails_when_email_is_missing(): void
    {
        $payload = $this->validPayload();
        unset($payload['email']);

        $response = $this->postJson('/api/v1/visitors', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_fails_when_email_is_invalid(): void
    {
        $response = $this->postJson('/api/v1/visitors', $this->validPayload([
            'email' => 'not-a-valid-email',
        ]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_fails_when_name_is_missing(): void
    {
        $payload = $this->validPayload();
        unset($payload['name']);

        $response = $this->postJson('/api/v1/visitors', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_fails_when_required_fields_are_absent(): void
    {
        $response = $this->postJson('/api/v1/visitors', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'name', 'surname', 'title', 'city', 'zip_code']);
    }

    public function test_store_fails_with_duplicate_email(): void
    {
        $this->makeVisitor(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/visitors', $this->validPayload([
            'email' => 'duplicate@example.com',
        ]));

        // The unique constraint on email will cause a DB error / 500,
        // so we assert it does NOT return a success status.
        $response->assertStatus(500);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/visitors/{visitor}
    // -------------------------------------------------------------------------

    public function test_show_returns_the_requested_visitor(): void
    {
        $visitor = $this->makeVisitor(['email' => 'show@example.com']);

        $response = $this->getJson("/api/v1/visitors/{$visitor->id}");

        $response->assertOk()
            ->assertJsonFragment(['email' => 'show@example.com']);
    }

    public function test_show_returns_404_for_unknown_visitor(): void
    {
        $response = $this->getJson('/api/v1/visitors/99999');

        $response->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // PATCH /api/v1/visitors/{visitor}
    // -------------------------------------------------------------------------

    public function test_update_modifies_the_visitor(): void
    {
        $visitor = $this->makeVisitor(['email' => 'old@example.com']);

        $response = $this->patchJson("/api/v1/visitors/{$visitor->id}", [
            'name' => 'UpdatedName',
            'city' => 'Lyon',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('visitors', [
            'id'   => $visitor->id,
            'name' => 'UpdatedName',
            'city' => 'Lyon',
        ]);
    }

    public function test_update_fails_when_email_is_invalid(): void
    {
        $visitor = $this->makeVisitor();

        $response = $this->patchJson("/api/v1/visitors/{$visitor->id}", [
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_returns_404_for_unknown_visitor(): void
    {
        $response = $this->patchJson('/api/v1/visitors/99999', [
            'name' => 'Ghost',
        ]);

        $response->assertNotFound();
    }

    public function test_update_preserves_existing_values_when_fields_are_omitted(): void
    {
        $visitor = $this->makeVisitor([
            'email' => 'keep@example.com',
            'name'  => 'OriginalName',
            'city'  => 'Marseille',
        ]);

        $response = $this->patchJson("/api/v1/visitors/{$visitor->id}", [
            'city' => 'Bordeaux',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('visitors', [
            'id'    => $visitor->id,
            'name'  => 'OriginalName',  // unchanged
            'city'  => 'Bordeaux',      // updated
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/v1/visitors/{visitor}
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_the_visitor(): void
    {
        $visitor = $this->makeVisitor();

        $response = $this->deleteJson("/api/v1/visitors/{$visitor->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('visitors', ['id' => $visitor->id]);
    }

    public function test_destroy_returns_404_for_unknown_visitor(): void
    {
        $response = $this->deleteJson('/api/v1/visitors/99999');

        $response->assertNotFound();
    }
}
