<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $logs_id
 * @property string $table_name
 * @property string $column_name
 * @property string|null $old_value
 * @property string|null $new_value
 */
class LogsColumn extends Model
{
    public $timestamps = false;

    protected $table = "logs_columns";

    protected $fillable = ["logs_id", "table_name", "column_name"];

    public function log(): BelongsTo
    {
        return $this->belongsTo(Logs::class, "logs_id");
    }
}
