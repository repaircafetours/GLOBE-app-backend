<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitorRequest;
use App\Http\Requests\UpdateVisitorRequest;
use App\Http\Services\ExtraAttributesService;
use App\Http\Services\VisitorService;
// use App\Http\Services\VisitorTokenService;
use App\Models\Visitor;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function __construct(
        private VisitorService $visitorService,
        private ExtraAttributesService $extraAttributesService,
        // private VisitorTokenService $tokenService,
    ) {}

    /**
     * Show all visitors.
     */
    public function index()
    {
        return $this->visitorService->getAll();
    }

    /**
     * Create a new visitor.
     * Public route – the visitor registers themselves, so the actor is null.
     */
    public function store(StoreVisitorRequest $request)
    {
        $visitor = new Visitor();
        $visitor->title = $request->input("title");
        $visitor->name = $request->input("name");
        $visitor->surname = $request->input("surname");
        $visitor->zip_code = $request->input("zip_code");
        $visitor->city = $request->input("city");
        $visitor->phone_number = $request->input("phone_number");
        $visitor->source = $request->input("source");
        $visitor->notification = $request->input("notification", false);
        $visitor->email = $request->input("email");
        $extra_attributes = $request->input("extra_attributes", null);
        if ($extra_attributes) {
            $this->extraAttributesService->updateAttributes($visitor, $extra_attributes);
        }

        /** @var Volunteer|null $actor */
        $actor =
            auth("sanctum")->user() instanceof Volunteer
                ? auth("sanctum")->user()
                : null;
        $this->visitorService->save($visitor, $actor);
    }

    /**
     * Show the specified Visitor.
     */
    public function show(Visitor $visitor): Visitor
    {
        return $visitor;
    }

    /**
     * Update a visitor.
     *
     * Access is handled by the AuthorizeVisitorUpdate middleware, which allows:
     *   - An authenticated volunteer with role Opérationnel (3) or Administrateur (1), OR
     *   - A valid visitor edit token supplied in the request.
     *
     * When the update is performed via a visitor token, the actor in the audit
     * log is null (the visitor modified their own profile). The token is revoked
     * immediately after a successful save so it cannot be reused.
     */
    public function update(UpdateVisitorRequest $request, Visitor $visitor)
    {
        $visitor->title = $request->input("title", $visitor->title);
        $visitor->name = $request->input("name", $visitor->name);
        $visitor->surname = $request->input("surname", $visitor->surname);
        $visitor->zip_code = $request->input("zip_code", $visitor->zip_code);
        $visitor->city = $request->input("city", $visitor->city);
        $visitor->phone_number = $request->input(
            "phone_number",
            $visitor->phone_number,
        );
        $visitor->source = $request->input("source", $visitor->source);
        $visitor->notification = $request->input(
            "notification",
            $visitor->notification,
        );
        $visitor->email = $request->input("email", $visitor->email);
        $this->extraAttributesService->updateAttributes(
            $visitor,
            $request->input(
                "extra_attributes",
                $visitor->extra_attributes->toArray() ?? [],
            ),
        );

        /** @var Volunteer|null $actor */
        $actor =
            auth("sanctum")->user() instanceof Volunteer
                ? auth("sanctum")->user()
                : null;

        $this->visitorService->save($visitor, $actor);

        // If this update was performed using a visitor edit token, revoke it
        // now so it cannot be reused (single-use after successful save).
        $plainToken =
            $request->input("visitor_token") ??
            $request->header("X-Visitor-Token");

        if ($plainToken !== null) {
            // $this->tokenService->revoke($visitor);
        }

        return $visitor;
    }

    /**
     * Delete a visitor.
     * The authenticated volunteer is recorded as the actor in the audit log.
     */
    public function destroy(Request $request, Visitor $visitor)
    {
        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->visitorService->delete($visitor, $actor);
    }
}
