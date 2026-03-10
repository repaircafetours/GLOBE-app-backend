<?php

namespace App\Models\Logs;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table pivot logs_visitors.
 * Relie un Visitor à une entrée Logs.
 *
 * @property int $volunteer_id
 * @property int $logs_id
 */
class LogsVolunteer extends Model
{
    public $timestamps = false;

    protected $table = "logs_volunteers";

    protected $fillable = ["volunteer_id", "logs_id"];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(Logs::class, "logs_id");
    }
}
