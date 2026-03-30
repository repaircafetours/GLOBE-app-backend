<?php

namespace App\Models\Logs;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Logs\LogsColumn;

/**
 * @property int             $id
 * @property \Carbon\Carbon  $updated_at
 * @property int|null        $volunteer_id
 * @property Volunteer       $volunteer
 */
class Logs extends Model
{
    const UPDATED_AT = "updated_at";
    const CREATED_AT = null;

    protected $fillable = ["volunteer_id", "updated_at"];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Les colonnes modifiées pour ce log, issues de information_schema.
     * Stockées directement avec table_name, column_name, old_value, new_value.
     */
    public function columns(): HasMany
    {
        return $this->hasMany(LogsColumn::class, "logs_id");
    }
}
