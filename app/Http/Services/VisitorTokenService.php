<?php

namespace App\Http\Services;

use App\Mail\VisitorEditMail;
use App\Models\Visitor;
use App\Models\VisitorEditToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VisitorTokenService
{
    private const EXPIRY_MINUTES = 60;

    /**
     * Generate a new edit token for the given visitor.
     * Any previously issued token for this visitor is revoked first.
     *
     * @return string The plain-text token to be transmitted to the visitor.
     */
    public function generate(Visitor $visitor): string
    {
        // Revoke any existing token for this visitor
        VisitorEditToken::where("visitor_id", $visitor->id)->delete();

        $plainToken = Str::random(64);

        VisitorEditToken::create([
            "visitor_id" => $visitor->id,
            "token" => hash("sha256", $plainToken),
            "expires_at" => now()->addMinutes(self::EXPIRY_MINUTES),
            "created_at" => now(),
        ]);

        return $plainToken;
    }

    public function send(Visitor $visitor): void
    {
        $plainToken = $this->generate($visitor);

        $url =
            config("app.frontend_url_edit_visitor") .
            "/edit?visitor={$visitor->id}&token={$plainToken}";

        Mail::to($visitor->email)->send(new VisitorEditMail($visitor, $url));
    }

    /**
     * Check whether the supplied plain-text token is valid for the given visitor.
     * An expired token is treated as invalid.
     * The token is NOT consumed so the visitor can make several PATCH calls
     * within the 10-minute window.
     */
    public function isValid(Visitor $visitor, string $plainToken): bool
    {
        return VisitorEditToken::where("visitor_id", $visitor->id)
            ->where("token", hash("sha256", $plainToken))
            ->where("expires_at", ">", now())
            ->exists();
    }

    /**
     * Explicitly revoke the token of a visitor (e.g. after a successful update).
     */
    public function revoke(Visitor $visitor): void
    {
        VisitorEditToken::where("visitor_id", $visitor->id)->delete();
    }
}
