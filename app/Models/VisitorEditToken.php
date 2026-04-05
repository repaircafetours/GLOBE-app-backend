<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int             $id
 * @property int             $visitor_id
 * @property string          $token       SHA-256 hash of the plain token
 * @property \Carbon\Carbon  $expires_at
 * @property \Carbon\Carbon  $created_at
 * @property Visitor         $visitor
 */
class VisitorEditToken extends Model
{
    public $timestamps = false;

    protected $table = "visitor_edit_tokens";

    protected $fillable = ["visitor_id", "token", "expires_at", "created_at"];

    protected $casts = [
        "expires_at"  => "datetime",
        "created_at"  => "datetime",
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Whether this token has passed its expiry date.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
