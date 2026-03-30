<?php

namespace App\Models\Logs;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table pivot logs_visitors.
 * Relie un Visitor à une entrée Logs.
 *
 * @property int $visitor_id
 * @property int $logs_id
 */
class LogsVisitor extends Model
{
    public $timestamps = false;

    protected $table = "logs_visitors";

    protected $fillable = ["visitor_id", "logs_id"];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(Logs::class, "logs_id");
    }
}
