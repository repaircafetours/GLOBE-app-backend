<?php

namespace App\Models\Logs;

use App\Models\Item;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table pivot logs_items.
 * Relie un Item (et son Visitor possesseur) à une entrée Logs.
 *
 * @property int      $item_id
 * @property int      $logs_id
 * @property int|null $visitor_id  Visiteur propriétaire de l'objet au moment de la modification
 */
class LogsItem extends Model
{
    public $timestamps = false;

    protected $table = "logs_items";

    protected $fillable = ["item_id", "logs_id", "visitor_id"];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Le visiteur possesseur de l'objet au moment où le log a été créé.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(Logs::class, "logs_id");
    }
}
